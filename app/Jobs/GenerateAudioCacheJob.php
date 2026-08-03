<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class GenerateAudioCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $text;
    protected $theme;
    protected $tone;
    protected $attemptTraceId;

    // Valeurs par défaut, centralisées ici pour que resolveAudioPath() (utilisé aussi
    // par AudioManageService pour le nettoyage) calcule toujours le même chemin que handle().
    public const DEFAULT_THEME = 'Philosophie générale';
    public const DEFAULT_TONE = 'Empathique et naturel';

    /**
     * Nombre de tentatives réelles autorisées avant échec définitif.
     * Nécessaire pour que la rotation de clés ait un sens : sans un tries > 1,
     * il n'y a jamais de seconde tentative pour essayer une autre clé.
     */
    public $tries = 3;

    /**
     * Crée une nouvelle instance de Job enrichie avec le contexte.
     */
    public function __construct(string $text, string $theme = self::DEFAULT_THEME, string $tone = self::DEFAULT_TONE)
    {
        $this->text = $text;
        $this->theme = $theme;
        $this->tone = $tone;
        // Identifiant stable pour cette série de tentatives : contrairement aux
        // propriétés modifiées pendant handle(), celui-ci fait partie du payload
        // sérialisé au moment du dispatch() et survit donc aux release().
        $this->attemptTraceId = uniqid('audio_', true);
    }

    /**
     * Calcule le chemin du fichier audio en cache pour un texte/thème/ton donnés.
     * Point d'entrée UNIQUE pour ce calcul — utilisé ici et par AudioManageService::
     * safeDeleteAudio(), pour que génération et nettoyage ne puissent plus jamais
     * calculer deux chemins différents pour le même contenu.
     */
    public static function resolveAudioPath(string $text, ?string $theme = null, ?string $tone = null): string
    {
        $theme ??= self::DEFAULT_THEME;
        $tone ??= self::DEFAULT_TONE;

        return 'audio/' . md5($text . $theme . $tone) . '.wav';
    }

    /**
     * Exécute le job.
     */
    public function handle(): void
    {
        // Étape 1 : Le Hash prédictif intègre le style pour correspondre au contrôleur
        $filePath = self::resolveAudioPath($this->text, $this->theme, $this->tone);

        // Si le fichier audio exact existe déjà, on stoppe pour économiser le quota
        if (Storage::disk('public')->exists($filePath)) {
            return;
        }

        // Récupération des configurations propres à Google
        // "Kore" est le nom exact (liste des 30 voix Gemini) — vérifie aussi la valeur dans ton .env
        $voiceName = config('services.gemini.voice', 'Kore');
        $baseUrl = rtrim(config('services.gemini.url'), '/');

        // Rotation aléatoire entre plusieurs clés/projets Gemini, pour ne pas épuiser un
        // seul quota. Sur retry, la clé qui vient d'échouer est exclue du tirage — voir
        // excludeApiKey() plus bas.
        $apiKeys = $this->getAvailableApiKeys();
        if (empty($apiKeys)) {
            Log::warning('Aucune clé API Gemini configurée pour la pré-génération audio en arrière-plan.');
            return;
        }
        $apiKey = $this->pickApiKey($apiKeys);
        Log::info("Génération audio via la clé Gemini {$this->maskApiKey($apiKey)} pour: " . substr($this->text, 0, 30));

        // Configuration du modèle cible (natif audio) et de l'endpoint
        // IMPORTANT : seul un modèle "-tts-" natif renvoie de l'audio. "gemini-flash-latest"
        // est un modèle de chat/raisonnement classique : il ignore responseModalities=AUDIO
        // et répond en texte — c'est exactement ce que montrent tes logs.
        $model = 'gemini-3.1-flash-tts-preview';
        $endpoint = "{$baseUrl}/{$model}:generateContent?key={$apiKey}";

        // Étape 2 : Construction du prompt. Un modèle TTS ne "discute" pas, il narre
        // uniquement — inutile de lui dire de ne pas répondre. On garde une consigne de
        // ton courte, puis un TRANSCRIPT clairement délimité (recommandation Google, pour
        // éviter que le modèle ne lise les instructions à voix haute au lieu du texte).
        $prompt = "Ton de narration : {$this->tone}, contexte [{$this->theme}].\n\nTRANSCRIPT (à lire mot à mot, sans aucun ajout) :\n{$this->text}";

        try {
            $client = new Client();
            $response = $client->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseModalities' => ['AUDIO'], // Exige un retour audio
                        'speechConfig' => [
                            'voiceConfig' => [
                                'prebuiltVoiceConfig' => [
                                    'voiceName' => $voiceName
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            // 1. Extraction robuste du flux audio (parcours dynamique du tableau 'parts')
            $base64Audio = null;
            if (isset($responseData['candidates'][0]['content']['parts'])) {
                foreach ($responseData['candidates'][0]['content']['parts'] as $part) {
                    if (isset($part['inlineData']['data'])) {
                        $base64Audio = $part['inlineData']['data'];
                        break;
                    } elseif (isset($part['inline_data']['data'])) {
                        $base64Audio = $part['inline_data']['data'];
                        break;
                    }
                }
            }

            // 2. Si le flux audio est introuvable
            if (!$base64Audio) {
                // On logue la réponse JSON brute exacte renvoyée par Google pour inspecter sa structure
                Log::error("Impossible d'extraire le flux audio Google AI Studio pour le texte: " . substr($this->text, 0, 30), [
                    'google_response' => $responseData
                ]);
                
                // On lève une exception pour que Laravel comprenne que le Job a ÉCHOUÉ !
                throw new \Exception("Structure audio invalide dans la réponse Google AI Studio.");
            }

            // 3. Décodage : Gemini TTS renvoie du PCM brut (16 bits, mono, 24 kHz) — il faut
            // lui ajouter un en-tête WAV avant de le stocker, sinon le fichier ne sera pas lisible.
            $pcmData = base64_decode($base64Audio);
            $wavData = $this->pcmToWav($pcmData);

            Storage::disk('public')->put($filePath, $wavData);
            
            Log::info("Audio (Google AI) généré et mis en cache avec succès pour: " . substr($this->text, 0, 30) . "...");

        } catch (\Exception $e) {
            Log::error('Erreur de pré-génération Google AI Studio (Job) pour le texte: ' . $this->text, ['error' => $e->getMessage()]);
            
            // On exclut cette clé de la prochaine tentative, qui en choisira une autre au hasard.
            $this->excludeApiKey($apiKey);

            // Relancer le job si c'est un problème temporaire de réseau ou de timeout API
            $this->release(30); 
        }
    }

    /**
     * Encapsule des données PCM brutes (retournées par Gemini TTS) dans un en-tête WAV
     * standard, pour obtenir un fichier audio lisible par un lecteur classique.
     */
    private function pcmToWav(string $pcmData, int $sampleRate = 24000, int $channels = 1, int $bitsPerSample = 16): string
    {
        $byteRate = $sampleRate * $channels * intdiv($bitsPerSample, 8);
        $blockAlign = $channels * intdiv($bitsPerSample, 8);
        $dataSize = strlen($pcmData);

        $header = 'RIFF'
            . pack('V', 36 + $dataSize)
            . 'WAVE'
            . 'fmt '
            . pack('V', 16)          // Taille du sous-bloc fmt (16 pour du PCM)
            . pack('v', 1)           // AudioFormat = 1 (PCM non compressé)
            . pack('v', $channels)
            . pack('V', $sampleRate)
            . pack('V', $byteRate)
            . pack('v', $blockAlign)
            . pack('v', $bitsPerSample)
            . 'data'
            . pack('V', $dataSize);

        return $header . $pcmData;
    }

    /**
     * Retourne la liste des clés API Gemini configurées (rotation multi-projets).
     */
    private function getAvailableApiKeys(): array
    {
        $keys = config('services.gemini.keys', []);
        return is_array($keys) ? array_values(array_filter($keys)) : [];
    }

    /**
     * Choisit une clé au hasard parmi celles pas encore essayées dans cette série de
     * tentatives (voir excludeApiKey ci-dessous), pour ne jamais retenter juste après
     * la clé qui vient d'échouer.
     */
    private function pickApiKey(array $apiKeys): string
    {
        $excluded = Cache::get($this->excludedKeysCacheKey(), []);
        $candidates = array_values(array_diff($apiKeys, $excluded));

        // Si toutes les clés ont déjà échoué durant cette série de tentatives, on repart
        // de la liste complète plutôt que de bloquer.
        if (empty($candidates)) {
            $candidates = $apiKeys;
        }

        return $candidates[array_rand($candidates)];
    }

    /**
     * Marque une clé comme ayant échoué pour cette série de tentatives (survit au
     * release() car stockée en dehors de l'objet Job, contrairement à une simple
     * propriété).
     */
    private function excludeApiKey(string $apiKey): void
    {
        $excluded = Cache::get($this->excludedKeysCacheKey(), []);
        $excluded[] = $apiKey;
        Cache::put($this->excludedKeysCacheKey(), array_values(array_unique($excluded)), now()->addMinutes(5));
    }

    private function excludedKeysCacheKey(): string
    {
        return "gemini_tts_excluded_keys_{$this->attemptTraceId}";
    }

    /**
     * Masque une clé API pour les logs (n'affiche que les 4 derniers caractères).
     */
    private function maskApiKey(string $apiKey): string
    {
        return '...' . substr($apiKey, -4);
    }

    /**
     * Gère l'échec définitif du job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Échec définitif de la génération audio Google AI Studio (Ludo Philo).', [
            'texte' => $this->text,
            'theme' => $this->theme,
            'tonalite' => $this->tone,
            'erreur' => $exception->getMessage(),
        ]);
    }
}