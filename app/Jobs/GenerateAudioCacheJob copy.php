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

    /**
     * Create a new job instance.
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileName = md5($this->text) . '.mp3';
        $filePath = 'audio/' . $fileName;

        // Étape 1 : Si le fichier existe déjà, on stoppe le job pour économiser le quota API
        if (Storage::disk('public')->exists($filePath)) {
            return;
        }

        $apiKey = env('ELEVENLABS_API_KEY');
        $voiceId = env('ELEVENLABS_VOICE_ID');

        if (!$apiKey || !$voiceId) {
            Log::warning('Clés ElevenLabs manquantes pour la pré-génération en arrière-plan.');
            return;
        }

        // Étape 2 : Appel à l'API ElevenLabs
        try {
            $client = new Client();
            $response = $client->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                'headers' => [
                    'Accept' => 'audio/mpeg',
                    'Content-Type' => 'application/json',
                    'xi-api-key' => $apiKey,
                ],
                'json' => [
                    'text' => $this->text,
                    'model_id' => 'eleven_multilingual_v2',
                    'voice_settings' => [
                        'stability' => 0.5,
                        'similarity_boost' => 0.5,
                    ],
                ],
                'stream' => true,
            ]);

            // Sauvegarde du fichier audio généré
            Storage::disk('public')->put($filePath, $response->getBody());
            
            Log::info("Audio généré et mis en cache avec succès pour: " . substr($this->text, 0, 30) . "...");

        } catch (\Exception $e) {
            Log::error('Erreur de pré-génération ElevenLabs (Job) pour le texte: ' . $this->text, ['error' => $e->getMessage()]);
            
            // Optionnel : Relancer le job en cas d'échec de l'API (ex: timeout)
            // $this->fail($e); 
        }
    }

    /**
     * Gère l'échec définitif du job.
     */
    public function failed(\Throwable $exception): void
    {
        // 1. Loguer l'erreur avec un niveau de criticité élevé
        \Illuminate\Support\Facades\Log::critical('Échec définitif de la génération audio ElevenLabs.', [
            'texte' => $this->text,
            'erreur' => $exception->getMessage(),
        ]);

        // 2. (Optionnel) Alerter l'administrateur
        // Tu pourrais utiliser le système de notification de Laravel ici :
        // Mail::to('admin@tonjeu.com')->send(new AudioGenerationFailedMail($this->text));

        // 3. (Optionnel) Mettre à jour la base de données
        // Si tu avais gardé l'ID de la question au lieu du texte, 
        // tu pourrais marquer la question avec un statut "erreur_audio = true"
    }
    
}