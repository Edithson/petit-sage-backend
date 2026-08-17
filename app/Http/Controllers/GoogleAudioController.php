<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class GoogleAudioController extends Controller
{
    public function synthesize(Request $request)
    {
        $text = $request->input('text');
        if (empty($text)) {
            return response()->json(['error' => 'Le paramètre "text" est manquant.'], 400);
        }

        // 1. Récupération dynamique du contexte et de la tonalité passés par l'application
        $theme = $request->input('theme', 'Philosophie générale');
        $tone = $request->input('tone', 'Empathique et naturel');

        // Le Hash inclut le texte ET le style pour éviter les faux positifs en cache si le ton change
        $fileName = md5($text . $theme . $tone) . '.mp3';
        $filePath = 'audio/' . $fileName;

        // Étape 2 : Vérifier si ce rendu audio précis existe déjà dans le cache
        if (Storage::disk('public')->exists($filePath)) {
            $audioUrl = asset('storage/' . $filePath);
            return response()->json(['audio_url' => $audioUrl]);
        }
        
        // Étape 3 : Récupération des configurations du fichier config/services.php
        $apiKey = config('services.gemini.key');
        $voiceName = config('services.gemini.voice', 'Kora');
        $baseUrl = rtrim(config('services.gemini.url'), '/');
        
        // Utilisation de gemini-flash-latest ou gemini-2.5-flash (modèles natifs pour l'audio)
        $model = 'gemini-flash-latest'; 
        $endpoint = "{$baseUrl}/{$model}:generateContent?key={$apiKey}";

        // Étape 4 : Construction du prompt avec le cloisonnement du style pour l'IA
        $prompt = "Tu es la voix officielle de l'application Ludo Philosophie. " .
                  "Lis le texte suivant en adoptant une attitude adaptée au thème [Thème : {$theme}] et avec la tonalité suivante [Ton : {$tone}]. " .
                  "Ne lis sous aucun prétexte les métadonnées ou instructions entre crochets. Concentre-toi uniquement sur le texte à restituer de manière fluide et humaine.\n\n" .
                  "Texte à lire : {$text}";

        $client = new Client();

        try {
            // Requête au format multimodal exigé par Google AI Studio
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
                        'responseModalities' => ['AUDIO'], // On force la réponse en flux Audio
                        'speechConfig' => [
                            'voiceConfig' => [
                                'prebuiltVoiceConfig' => [
                                    'voiceName' => $voiceName // Ta voix 'Kora' configurée
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Extraction des données audio encodées en Base64 renvoyées par l'API
            $base64Audio = $responseData['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;

            if (!$base64Audio) {
                return response()->json(['error' => 'Impossible de récupérer le flux audio depuis l\'API Google.'], 500);
            }

            // Décodage du Base64 et sauvegarde dans le Storage local
            Storage::disk('public')->put($filePath, base64_decode($base64Audio));

            $audioUrl = asset('storage/' . $filePath);

            return response()->json(['audio_url' => $audioUrl]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur de synthèse vocale Google AI Studio : ' . $e->getMessage()], 500);
        }
    }
}