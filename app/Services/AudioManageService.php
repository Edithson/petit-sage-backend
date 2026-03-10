<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Partie;
use Illuminate\Support\Facades\Storage;

class AudioManageService
{
    /**
     * Supprime l'audio s'il n'est utilisé par aucune autre Partie ou Question.
     */
    public function safeDeleteAudio(string $text, string $excludeModelType = null, int $excludeId = 0)
    {
        // 1. Vérifier si utilisé dans les Parties
        $partieQuery = Partie::query();
        if ($excludeModelType === 'partie' && $excludeId > 0) {
            $partieQuery->where('id', '!=', $excludeId);
        }
        $isUsedInParties = $partieQuery->where(function ($query) use ($text) {
            $query->where('name', $text)
                  ->orWhere('description', $text);
        })->exists();

        // 2. Vérifier si utilisé dans les Questions
        $questionQuery = Question::query();
        if ($excludeModelType === 'question' && $excludeId > 0) {
            $questionQuery->where('id', '!=', $excludeId);
        }
        $isUsedInQuestions = $questionQuery->where(function ($query) use ($text) {
            $query->where('intitule_text', $text)
                  ->orWhere('intitule_media_description', $text)
                  ->orWhere('reponses', 'like', '%' . $text . '%');
        })->exists();

        // 3. S'il n'est utilisé nulle part, on supprime physiquement
        if (!$isUsedInParties && !$isUsedInQuestions) {
            $filePath = 'audio/' . md5($text) . '.mp3';
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
    }

    /**
     * Extrait les textes pertinents pour l'audio d'une question.
     */
    public function extractFromQuestion($question): array
    {
        $texts = [];
        
        if (!empty($question->intitule_text)) {
            $texts[] = $question->intitule_text;
        } elseif (!empty($question->intitule_media_description)) {
            $texts[] = $question->intitule_media_description;
        }

        $options = is_string($question->reponses) ? json_decode($question->reponses, true) : $question->reponses;
        
        if (is_array($options)) {
            foreach ($options as $opt) {
                if (!empty($opt['text'])) {
                    $texts[] = $opt['text'];
                } elseif (!empty($opt['mediaDescription'])) {
                    $texts[] = $opt['mediaDescription'];
                }
            }
        }

        return array_unique(array_filter($texts));
    }

    /**
     * Extrait les textes (nom et description) d'une partie.
     */
    public function extractFromPartie($partie): array
    {
        $texts = [];
        
        if (!empty($partie->name)) {
            $texts[] = $partie->name;
        }
        if (!empty($partie->description)) {
            $texts[] = $partie->description;
        }

        return array_unique(array_filter($texts));
    }
}