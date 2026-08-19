<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAudioCacheJob;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    /**
     * Déclenche en arrière-plan la génération d'un audio manquant (appelé par le
     * frontend quand il bascule sur la voix native faute de fichier en cache).
     * Ne fait qu'empiler un Job — jamais de génération synchrone, pour que la
     * réponse HTTP reste quasi instantanée.
     */
    public function precache(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        GenerateAudioCacheJob::dispatch($validated['text']);

        return response()->json(['status' => 'queued'], 202);
    }
}