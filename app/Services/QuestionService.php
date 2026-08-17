<?php

namespace App\Services;

use App\Models\Question;
use App\Jobs\GenerateAudioCacheJob;
use App\Services\AudioManageService;
use Illuminate\Support\Facades\Storage;

class QuestionService
{
    protected $audioManageService;

    public function __construct(AudioManageService $audioManageService)
    {
        $this->audioManageService = $audioManageService;
    }

    /**
     * Crée une nouvelle question et gère les fichiers.
     */
    public function createQuestion(array $validatedData, $request)
    {
        $intituleText = null;
        $intituleMediaUrl = null;
        $intituleMediaDescription = null;

        // Gestion du téléchargement du média de l'intitulé
        if ($validatedData['intitule']['contentType'] === 'text') {
            $intituleText = $validatedData['intitule']['text'];
        } elseif ($validatedData['intitule']['contentType'] === 'media') {
            $intituleMediaDescription = $validatedData['intitule']['mediaDescription'];
            if ($validatedData['intitule']['mediaSourceType'] === 'file' && $request->hasFile('intitule.mediaFile')) {
                $path = $request->file('intitule.mediaFile')->store('questions', 'public');
                $intituleMediaUrl = Storage::url($path);
            } elseif ($validatedData['intitule']['mediaSourceType'] === 'url') {
                $intituleMediaUrl = $validatedData['intitule']['mediaUrl'];
            }
        }

        // Traitement des options de réponse et téléchargement des médias associés
        $processedOptions = [];
        foreach ($validatedData['options'] as $key => $option) {
            $optionData = [
                'contentType' => $option['contentType'],
                'isCorrect' => (bool)$option['isCorrect'],
                'mediaDescription' => $option['mediaDescription'] ?? null,
            ];

            if ($option['contentType'] === 'text') {
                $optionData['text'] = $option['text'];
            } elseif ($option['contentType'] === 'media') {
                $optionData['mediaSourceType'] = $option['mediaSourceType'];
                if ($option['mediaSourceType'] === 'file' && $request->hasFile("options.{$key}.mediaFile")) {
                    $path = $request->file("options.{$key}.mediaFile")->store('questions', 'public');
                    $optionData['mediaUrl'] = Storage::url($path);
                } elseif ($option['mediaSourceType'] === 'url') {
                    $optionData['mediaUrl'] = $option['mediaUrl'];
                }
            }
            $processedOptions[] = $optionData;
        }

        // Détection du numéro de la question
        $maxNumero = Question::where('thematique_id', $validatedData['theme'])
            ->where('partie_id', $validatedData['partie_id'])
            ->max('numero');
        $newNumero = $maxNumero ? $maxNumero + 1 : 1;

        $questionData = [
            'intitule_text' => $intituleText,
            'intitule_media_url' => $intituleMediaUrl,
            'intitule_media_description' => $intituleMediaDescription,
            'thematique_id' => (int)$validatedData['theme'],
            'partie_id' => (int)$validatedData['partie_id'],
            'degre_difficulte' => (int)$validatedData['difficulte'],
            'type_reponse' => $validatedData['typeReponse'],
            'explication' => $validatedData['explication'] ?? null,
            'indice' => $validatedData['indice'] ?? null,
            'reponses' => json_encode($processedOptions),
            'numero' => $newNumero,
        ];

        $question = Question::create($questionData);

        // Lancement de la mise en cache audio
        $this->precacheQuestionAudios($intituleText, $intituleMediaDescription, $processedOptions);

        return $question;
    }

    /**
     * Met à jour une question existante et gère les fichiers.
     */
    public function updateQuestion(Question $question, array $validatedData, $request)
    {
        $intituleText = null;
        $intituleMediaUrl = $question->intitule_media_url;
        $intituleMediaDescription = null;

        $validatedIntitule = $validatedData['intitule'];

        $oldIntituleFilePath = null;
        if ($question->intitule_media_url && str_contains($question->intitule_media_url, '/storage/')) {
            $oldIntituleFilePath = str_replace('/storage/', '', $question->intitule_media_url);
        }

        if ($validatedIntitule['contentType'] === 'text') {
            $intituleText = $validatedIntitule['text'];
            if ($oldIntituleFilePath) {
                Storage::disk('public')->delete($oldIntituleFilePath);
            }
            $intituleMediaUrl = null;
            $intituleMediaDescription = null;
        } elseif ($validatedIntitule['contentType'] === 'media') {
            $intituleMediaDescription = $validatedIntitule['mediaDescription'];

            if ($validatedIntitule['mediaSourceType'] === 'file') {
                if ($request->hasFile('intitule.mediaFile')) {
                    if ($oldIntituleFilePath) {
                        Storage::disk('public')->delete($oldIntituleFilePath);
                    }
                    $path = $request->file('intitule.mediaFile')->store('questions', 'public');
                    $intituleMediaUrl = Storage::url($path);
                }
            } elseif ($validatedIntitule['mediaSourceType'] === 'url') {
                $newMediaUrl = $validatedIntitule['mediaUrl'];
                if ($oldIntituleFilePath && $newMediaUrl !== $question->intitule_media_url) {
                    Storage::disk('public')->delete($oldIntituleFilePath);
                }
                $intituleMediaUrl = $newMediaUrl;
            }
        }

        $processedOptions = [];
        $oldOptions = is_string($question->reponses) ? json_decode($question->reponses, true) : $question->reponses;
        if (!is_array($oldOptions)) {
            $oldOptions = [];
        }

        foreach ($validatedData['options'] as $key => $option) {
            $optionData = [
                'contentType' => $option['contentType'],
                'isCorrect' => (bool)$option['isCorrect'],
                'mediaDescription' => $option['mediaDescription'] ?? null,
            ];

            $oldOptionMediaUrl = $oldOptions[$key]['mediaUrl'] ?? null;
            $optionData['mediaUrl'] = $oldOptionMediaUrl;

            if ($option['contentType'] === 'text') {
                $optionData['text'] = $option['text'];
                $optionData['mediaUrl'] = null;
                if ($oldOptionMediaUrl && str_contains($oldOptionMediaUrl, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptionMediaUrl));
                }
            } elseif ($option['contentType'] === 'media') {
                $optionData['mediaSourceType'] = $option['mediaSourceType'];

                if ($option['mediaSourceType'] === 'file') {
                    if ($request->hasFile("options.{$key}.mediaFile")) {
                        if ($oldOptionMediaUrl && str_contains($oldOptionMediaUrl, '/storage/')) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptionMediaUrl));
                        }
                        $path = $request->file("options.{$key}.mediaFile")->store('questions', 'public');
                        $optionData['mediaUrl'] = Storage::url($path);
                    }
                } elseif ($option['mediaSourceType'] === 'url') {
                    $newMediaUrl = $option['mediaUrl'];
                    if ($oldOptionMediaUrl && $newMediaUrl !== $oldOptionMediaUrl && str_contains($oldOptionMediaUrl, '/storage/')) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptionMediaUrl));
                    }
                    $optionData['mediaUrl'] = $newMediaUrl;
                }
            }
            $processedOptions[] = $optionData;
        }

        // Extrait les anciens textes pour nettoyer l'audio TTS
        $oldTexts = $this->audioManageService->extractFromQuestion($question);

        $question->update([
            'intitule_text' => $intituleText,
            'intitule_media_url' => $intituleMediaUrl,
            'intitule_media_description' => $intituleMediaDescription,
            'thematique_id' => (int)$validatedData['theme'],
            'partie_id' => (int)$validatedData['partie_id'],
            'degre_difficulte' => (int)$validatedData['difficulte'],
            'type_reponse' => $validatedData['typeReponse'],
            'explication' => $validatedData['explication'] ?? null,
            'indice' => $validatedData['indice'] ?? null,
            'reponses' => json_encode($processedOptions),
        ]);

        // Ne nettoyer que les textes qui ont réellement disparu : un texte encore
        // présent après la mise à jour (option inchangée, par exemple) ne doit jamais
        // être envoyé à safeDeleteAudio(), sous peine de le voir supprimé puis
        // régénéré pour rien (safeDeleteAudio exclut la question courante de sa
        // vérification "encore utilisé ailleurs", donc un texte toujours utilisé
        // par CETTE question, mais par aucune autre, semblerait à tort orphelin).
        $newTexts = $this->audioManageService->extractFromQuestion($question);
        $removedTexts = array_diff($oldTexts, $newTexts);

        foreach ($removedTexts as $oldText) {
            $this->audioManageService->safeDeleteAudio($oldText, 'question', $question->id);
        }

        // Lancement de la mise en cache audio pour les nouveaux textes
        $this->precacheQuestionAudios($intituleText, $intituleMediaDescription, $processedOptions);

        return $question;
    }

    /**
     * Supprime une question et ses audios, et réorganise les numéros.
     */
    public function deleteQuestion(Question $question)
    {
        // 1. Réorganiser les numéros des autres questions de la même partie
        Question::where('thematique_id', $question->thematique_id)
            ->where('partie_id', $question->partie_id)
            ->where('numero', '>', $question->numero)
            ->decrement('numero');

        // 2. Supprimer le fichier de l'intitulé si c'est un fichier stocké localement
        if ($question->intitule_media_url && str_contains($question->intitule_media_url, '/storage/')) {
            $filePath = str_replace('/storage/', '', $question->intitule_media_url);
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        // 3. Supprimer les fichiers des options stockés localement
        $options = is_string($question->reponses) ? json_decode($question->reponses, true) : $question->reponses;
        if (is_array($options)) {
            foreach ($options as $option) {
                if (isset($option['mediaUrl']) && str_contains($option['mediaUrl'], '/storage/')) {
                    $filePath = str_replace('/storage/', '', $option['mediaUrl']);
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
        }

        // 4. Extraire les textes pour nettoyer les fichiers audio TTS
        $texts = $this->audioManageService->extractFromQuestion($question);

        // 5. Supprimer la question (Soft delete)
        $question->delete();

        // 6. Supprimer physiquement les audios s'ils ne sont plus utilisés par d'autres questions/parties
        foreach ($texts as $text) {
            $this->audioManageService->safeDeleteAudio($text, 'question', $question->id);
        }
    }

    /**
     * Extrait les textes et lance les Jobs de génération audio en arrière-plan.
     */
    public function precacheQuestionAudios($intituleText, $intituleMediaDescription, array $options)
    {
        $textsToSynthesize = [];

        if (!empty($intituleText)) {
            $textsToSynthesize[] = $intituleText;
        } elseif (!empty($intituleMediaDescription)) {
            $textsToSynthesize[] = $intituleMediaDescription;
        }

        foreach ($options as $option) {
            if (!empty($option['text'])) {
                $textsToSynthesize[] = $option['text'];
            } elseif (!empty($option['mediaDescription'])) {
                $textsToSynthesize[] = $option['mediaDescription'];
            }
        }

        $textsToSynthesize = array_unique(array_filter($textsToSynthesize));

        foreach ($textsToSynthesize as $text) {
            GenerateAudioCacheJob::dispatch($text);
        }
    }
}