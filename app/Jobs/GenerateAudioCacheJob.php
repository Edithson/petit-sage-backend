<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class GenerateAudioCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $text;
    protected $theme;
    protected $tone;

    /**
     * Crée une nouvelle instance de Job enrichie avec le contexte.
     */
    public function __construct(string $text, string $theme = 'Philosophie générale', string $tone = 'Empathique et naturel')
    {
        $this->text = $text;
        $this->theme = $theme;
        $this->tone = $tone;
    }

    /**
     * Exécute le job.
     */
    public function handle(): void
    {
        // Étape 1 : Le Hash prédictif intègre le style pour correspondre au contrôleur
        // NB : extension .wav — Gemini TTS renvoie du PCM brut, pas un .mp3 (voir plus bas)
        $fileName = md5($this->text . $this->theme . $this->tone) . '.wav';
        $filePath = 'audio/' . $fileName;

        // Si le fichier audio exact existe déjà, on stoppe pour économiser le quota
        if (Storage::disk('public')->exists($filePath)) {
            return;
        }

        // Récupération des configurations propres à Google
        $apiKey = config('services.gemini.key');
        // "Kore" est le nom exact (liste des 30 voix Gemini) — vérifie aussi la valeur dans ton .env
        $voiceName = config('services.gemini.voice', 'Kore');
        $baseUrl = rtrim(config('services.gemini.url'), '/');

        if (!$apiKey) {
            Log::warning('Clé API Gemini manquante pour la pré-génération audio en arrière-plan.');
            return;
        }

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