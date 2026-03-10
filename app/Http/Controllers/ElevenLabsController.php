<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class ElevenLabsController extends Controller
{
    public function synthesize(Request $request)
    {
        $text = $request->input('text');
        if (empty($text)) {
            return response()->json(['error' => 'Le paramètre "text" est manquant.'], 400);
        }

        // Création d'un hash unique pour le texte pour un nom de fichier prédictible
        $fileName = md5($text) . '.mp3';
        $filePath = 'audio/' . $fileName;

        // Étape 1 : Vérifier si le fichier existe déjà dans le cache
        if (Storage::disk('public')->exists($filePath)) {
            $audioUrl = asset('storage/' . $filePath);
            return response()->json(['audio_url' => $audioUrl]);
        }
        
        // Si le fichier n'existe pas, on procède à l'appel API
        $apiKey = env('ELEVENLABS_API_KEY');
        $voiceId = env('ELEVENLABS_VOICE_ID');
        
        $client = new Client();

        try {
            $response = $client->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                'headers' => [
                    'Accept' => 'audio/mpeg',
                    'Content-Type' => 'application/json',
                    'xi-api-key' => $apiKey,
                ],
                'json' => [
                    'text' => $text,
                    'model_id' => 'eleven_multilingual_v2',
                    'voice_settings' => [
                        'stability' => 0.5,
                        'similarity_boost' => 0.5,
                    ],
                ],
                'stream' => true,
            ]);

            // Sauvegarde du fichier audio
            Storage::disk('public')->put($filePath, $response->getBody());

            $audioUrl = asset('storage/' . $filePath);

            return response()->json(['audio_url' => $audioUrl]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur de synthèse vocale: ' . $e->getMessage()], 500);
        }
    }
}