<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PackageControlleur;
use App\Jobs\GenerateAudioCacheJob;

class QuestionController extends Controller
{
    public function giveQuestionNumber(){
        $listQuestions = Question::all();
        foreach($listQuestions as $key => $question){
            $question->numero = $key + 1;
            $question->save();
        }
        return response()->json([
            'message' => 'Numéros des questions mis à jour avec succès!',
            'data' => $listQuestions
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // 1. On ne sélectionne QUE les colonnes nécessaires pour l'affichage du tableau
            $query = Question::select([
                'id', 
                'intitule_text', 
                'intitule_media_description', 
                'thematique_id', 
                'partie_id', 
                'degre_difficulte'
            ])
            ->with([
                'thematique:id,name', // On garde uniquement l'id et le nom
                'partie:id,numero'    // Idem pour la partie
            ])->orderBy('created_at', 'desc');

            // 2. Recherche Backend optimisée
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('intitule_text', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('intitule_media_description', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            // 3. Filtre par thématique
            if ($request->filled('theme_id') && $request->theme_id !== 'all') {
                $query->where('thematique_id', $request->theme_id);
            }

            // 4. Pagination (15 par défaut)
            $perPage = $request->get('per_page', 15);
            $questions = $query->paginate($perPage);

            return PackageControlleur::successResponse(
                $questions,
                'Liste des questions récupérée avec succès'
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération questions', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des questions : '.$th->getMessage());
        }
    }

    public function showQuestionThematics($id)
    {
        try {
            $questions = Question::where('thematique_id', $id)->orderBy('numero')->get();
            return PackageControlleur::successResponse(
                $questions,
                'Liste des questions récupérée avec succès',
                ['count' => count($questions)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération questions', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des questions : '.$th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Gérer les requêtes OPTIONS
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json([], 200);
        }

        $user = $request->user();

        // Vérifications supplémentaires si nécessaire
        if (!$user) {
            \Log::error('Token invalide');
            return PackageControlleur::errorResponse('Token invalide', 401);
        }

        // Définition des règles de validation
        $rules = [
            'intitule' => 'required|array',
            'intitule.contentType' => 'required|string|in:text,media',
            'intitule.mediaSourceType' => 'nullable|string|in:url,file',
            // Validation conditionnelle pour le contenu texte ou média de l'intitulé
            'intitule.text' => 'nullable|string',
            'intitule.mediaDescription' => 'nullable|string',
            'intitule.mediaUrl' => 'nullable|string',
            'intitule.mediaFile' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480',
            // Validation des autres champs
            'theme' => 'required|integer|exists:thematiques,id',
            'partie_id' => 'required|integer|exists:parties,id',
            'difficulte' => 'required|string|in:1,2,3',
            'typeReponse' => 'required|string|in:unique,multiple',
            'explication' => 'nullable|string',
            'indice' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.isCorrect' => 'required|boolean',
            'options.*.contentType' => 'required|string|in:text,media',
            // Validation conditionnelle pour les options
            'options.*.text' => 'string|nullable',
            'options.*.mediaSourceType' => 'string|in:url,file',
            'options.*.mediaUrl' => 'url|nullable',
            'options.*.mediaDescription' => 'string|nullable',
        ];

        // Validation dynamique pour l'intitulé de la question
        if ($request->input('intitule.contentType') === 'text') {
            $rules['intitule.text'] = 'required|string';
        } elseif ($request->input('intitule.contentType') === 'media') {
            $rules['intitule.mediaDescription'] = 'required|string';
            if ($request->input('intitule.mediaSourceType') === 'url') {
                $rules['intitule.mediaUrl'] = 'required';
            } elseif ($request->input('intitule.mediaSourceType') === 'file') {
                // Validation pour les fichiers médias de l'intitulé (max 20MB)
                $rules['intitule.mediaFile'] = 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480';
            }
        }

        // Validation pour les fichiers médias des options (max 10MB par option)
        foreach ($request->input('options') as $key => $option) {
            if (isset($option['contentType']) && $option['contentType'] === 'media') {
                if (isset($option['mediaSourceType']) && $option['mediaSourceType'] === 'file') {
                    $rules["options.{$key}.mediaFile"] = 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:10240';
                }
            }
        }

        // Exécution de la validation
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Erreurs de validation', ['error' => $validator->errors()]);
            return PackageControlleur::errorResponse('Erreurs de validation'.$validator->errors(), 422);
        }

        $validatedData = $validator->validated();

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
                'isCorrect' => (bool)$option['isCorrect'], // S'assurer que c'est un booléen
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
        $maxNumero = Question::where('thematique_id', $validatedData['theme'])->where('partie_id', $validatedData['partie_id'])->max('numero');
        $newNumero = $maxNumero ? $maxNumero + 1 : 1;

        try {
            // Création de la question en base de données
            $data = [
                'intitule_text' => $intituleText,
                'intitule_media_url' => $intituleMediaUrl,
                'intitule_media_description' => $intituleMediaDescription,
                'thematique_id' => (int)$validatedData['theme'],
                'partie_id' => (int)$validatedData['partie_id'],
                'degre_difficulte' => (int)$validatedData['difficulte'],
                'type_reponse' => $validatedData['typeReponse'],
                'explication' => $validatedData['explication'],
                'indice' => $validatedData['indice'],
                'reponses' => json_encode($processedOptions), // Stocke le tableau d'objets des réponses en JSON
                'numero' => $newNumero,
            ];
            $question = Question::create($data);

            //Lancement de la mise en cache audio
            $this->precacheQuestionAudios($intituleText, $intituleMediaDescription, $processedOptions);

            return PackageControlleur::successResponse(
                $question,
                'Question crée avec succès',
                ['count' => 1]
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création question', ['error' => $e->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la création de la question : '.$e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $question = Question::with(['thematique', 'creator'])->find($id);
            if (!$question) {
                \Log::error('Question non trouvée.');
                return PackageControlleur::errorResponse('Question non trouvée.', 404, []);
            }
            return PackageControlleur::successResponse($question, 'Question trouvée avec succès.');
        } catch (\Throwable $th) {
            \Log::error('Erreur lors de la récupération de la question', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération de la question : '.$th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $question = Question::find($id);
        if (!$question) {
            \Log::error('Question non trouvée.');
            return PackageControlleur::errorResponse('Question non trouvée.', 404, []);
        }

        // Définition des règles de validation
        $rules = [
            'intitule' => 'required|array',
            'intitule.contentType' => 'required|string|in:text,media',
            'intitule.mediaSourceType' => 'nullable|string|in:url,file',
            'intitule.text' => 'nullable|string',
            'intitule.mediaDescription' => 'nullable|string',
            'intitule.mediaUrl' => 'nullable|string',
            'intitule.mediaFile' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480',
            'theme' => 'required|integer|exists:thematiques,id',
            'partie_id' => 'required|integer|exists:parties,id',
            'difficulte' => 'required|integer|in:1,2,3',
            'typeReponse' => 'required|string|in:unique,multiple',
            'explication' => 'nullable|string',
            'indice' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.isCorrect' => 'required|boolean',
            'options.*.contentType' => 'required|string|in:text,media',
            'options.*.text' => 'nullable|string',
            'options.*.mediaSourceType' => 'nullable|string|in:url,file',
            'options.*.mediaUrl' => 'nullable|string',
            'options.*.mediaDescription' => 'nullable|string',
        ];

        // Validation dynamique pour l'intitulé
        if ($request->input('intitule.contentType') === 'text') {
            $rules['intitule.text'] = 'required|string';
        } elseif ($request->input('intitule.contentType') === 'media') {
            $rules['intitule.mediaDescription'] = 'required|string';
            if ($request->input('intitule.mediaSourceType') === 'url') {
                $rules['intitule.mediaUrl'] = 'required|string';
            } elseif ($request->input('intitule.mediaSourceType') === 'file') {
                // Rendre le fichier optionnel si l'on ne veut pas le remplacer
                $rules['intitule.mediaFile'] = 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:20480';
            }
        }

        // Validation dynamique pour les options
        foreach ($request->input('options') as $key => $option) {
            if ($option['contentType'] === 'text') {
                $rules["options.{$key}.text"] = 'required|string';
            } elseif ($option['contentType'] === 'media') {
                if ($option['mediaSourceType'] === 'url') {
                    $rules["options.{$key}.mediaUrl"] = 'required|string';
                } elseif ($option['mediaSourceType'] === 'file') {
                    $rules["options.{$key}.mediaFile"] = 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,ogg,webm,mp3,wav|max:10240';
                }
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Erreurs de validation', ['error' => $validator->errors()]);
            return PackageControlleur::errorResponse('Erreurs de validation: '.$validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        // Déclarer les variables en début de fonction pour éviter les problèmes de portée
        $intituleText = null;
        $intituleMediaUrl = $question->intitule_media_url; // Initialiser avec la valeur existante
        $intituleMediaDescription = null;

        // GESTION DES MÉDIAS DE L'INTITULÉ
        $validatedIntitule = $validatedData['intitule'];

        // L'ancien chemin du fichier sur le disque
        $oldIntituleFilePath = null;
        if ($question->intitule_media_url && str_contains($question->intitule_media_url, '/storage/')) {
            $oldIntituleFilePath = str_replace('/storage/', '', $question->intitule_media_url);
        }

        // Cas 1: Le type de contenu est "text"
        if ($validatedIntitule['contentType'] === 'text') {
            $intituleText = $validatedIntitule['text'];
            // Supprimer l'ancien fichier s'il existait
            if ($oldIntituleFilePath) {
                Storage::disk('public')->delete($oldIntituleFilePath);
            }
            $intituleMediaUrl = null;
            $intituleMediaDescription = null;
        }
        // Cas 2: Le type de contenu est "media"
        elseif ($validatedIntitule['contentType'] === 'media') {
            $intituleMediaDescription = $validatedIntitule['mediaDescription'];

            // Cas 2a: La source du média est un "fichier"
            if ($validatedIntitule['mediaSourceType'] === 'file') {
                if ($request->hasFile('intitule.mediaFile')) {
                    // Un nouveau fichier a été téléchargé, on supprime l'ancien
                    if ($oldIntituleFilePath) {
                        Storage::disk('public')->delete($oldIntituleFilePath);
                    }
                    $path = $request->file('intitule.mediaFile')->store('questions', 'public');
                    $intituleMediaUrl = Storage::url($path);
                } else {
                    // Aucun nouveau fichier. On garde l'URL existante.
                    $intituleMediaUrl = $question->intitule_media_url;
                }
            }
            // Cas 2b: La source du média est une "URL"
            elseif ($validatedIntitule['mediaSourceType'] === 'url') {
                $newMediaUrl = $validatedIntitule['mediaUrl'];
                // Supprimer l'ancien fichier SI la nouvelle URL est différente de l'ancienne
                if ($oldIntituleFilePath && $newMediaUrl !== $question->intitule_media_url) {
                    Storage::disk('public')->delete($oldIntituleFilePath);
                }
                $intituleMediaUrl = $newMediaUrl;
            }
        }

        // Traitement des options de réponse et mise à jour des médias associés
        $processedOptions = [];
        $oldOptions = json_decode($question->reponses, true) ?? [];

        foreach ($validatedData['options'] as $key => $option) {
            $optionData = [
                'contentType' => $option['contentType'],
                'isCorrect' => (bool)$option['isCorrect'],
                'mediaDescription' => $option['mediaDescription'] ?? null,
            ];

            // Initialiser l'URL du média de l'option avec l'ancienne valeur si elle existe
            $oldOptionMediaUrl = $oldOptions[$key]['mediaUrl'] ?? null;
            $optionData['mediaUrl'] = $oldOptionMediaUrl; // Conserver par défaut l'ancienne URL

            // Logique de suppression et mise à jour
            // Cas 1: Le type de contenu est "text"
            if ($option['contentType'] === 'text') {
                $optionData['text'] = $option['text'];
                $optionData['mediaUrl'] = null; // Effacer l'URL du média

                // Supprimer l'ancien fichier s'il existait et que le type devient "text"
                if ($oldOptionMediaUrl && str_contains($oldOptionMediaUrl, '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptionMediaUrl));
                }
            }
            // Cas 2: Le type de contenu est "media"
            elseif ($option['contentType'] === 'media') {
                $optionData['mediaSourceType'] = $option['mediaSourceType'];

                // Cas 2a: La source du média est un "fichier"
                if ($option['mediaSourceType'] === 'file') {
                    if ($request->hasFile("options.{$key}.mediaFile")) {
                        // Un nouveau fichier a été téléchargé, on supprime l'ancien
                        if ($oldOptionMediaUrl && str_contains($oldOptionMediaUrl, '/storage/')) {
                            Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptionMediaUrl));
                        }
                        $path = $request->file("options.{$key}.mediaFile")->store('questions', 'public');
                        $optionData['mediaUrl'] = Storage::url($path);
                    } else {
                        // Aucun nouveau fichier. Conserver l'ancienne URL.
                        $optionData['mediaUrl'] = $oldOptionMediaUrl;
                    }
                }
                // Cas 2b: La source du média est une "URL"
                elseif ($option['mediaSourceType'] === 'url') {
                    $newMediaUrl = $option['mediaUrl'];
                    // Supprimer l'ancien fichier SI la nouvelle URL est différente de l'ancienne ET que l'ancienne était un fichier
                    if ($oldOptionMediaUrl && str_contains($oldOptionMediaUrl, '/storage/') && $newMediaUrl !== $oldOptionMediaUrl) {
                        Storage::disk('public')->delete(str_replace('/storage/', '', $oldOptionMediaUrl));
                    }
                    $optionData['mediaUrl'] = $newMediaUrl;
                }
            }
            $processedOptions[] = $optionData;
        }

        try {
            // 1. Capturer les textes avant modification
            $oldTexts = $this->extractTextsFromQuestion($question);

            // Réorganisation des numéros de questions
            if ($question->thematique_id != $validatedData['theme'] || $question->partie_id != $validatedData['partie_id']) {
                Question::where('thematique_id', $question->thematique_id)
                    ->where('partie_id', $question->partie_id)
                    ->where('numero', '>', $question->numero)
                    ->decrement('numero');
            }
            if ($question->thematique_id != $validatedData['theme'] || $question->partie_id != $validatedData['partie_id']) {
                $maxNumero = Question::where('thematique_id', $validatedData['theme'])
                    ->where('partie_id', $validatedData['partie_id'])
                    ->max('numero');
                $question->numero = $maxNumero ? $maxNumero + 1 : 1;
            }

            // Mise à jour des données
            $question->intitule_text = $intituleText;
            $question->intitule_media_url = $intituleMediaUrl;
            $question->intitule_media_description = $intituleMediaDescription;
            $question->thematique_id = $validatedData['theme'];
            $question->degre_difficulte = $validatedData['difficulte'];
            $question->type_reponse = $validatedData['typeReponse'];
            $question->explication = $validatedData['explication'];
            $question->indice = $validatedData['indice'] ?? null;
            $question->partie_id = $validatedData['partie_id'];
            $question->reponses = json_encode($processedOptions);

            $question->save();

            // 2. Capturer les textes après modification
            $newTexts = $this->extractTextsFromQuestion($question);

            // 3. Déterminer les différences
            $textsToDelete = array_diff($oldTexts, $newTexts); // Textes qui n'existent plus
            $textsToGenerate = array_diff($newTexts, $oldTexts); // Nouveaux textes ajoutés

            // 4. Nettoyer les anciens audios (en toute sécurité)
            foreach ($textsToDelete as $text) {
                $this->safeDeleteAudio($text, $question->id);
            }

            // 5. Générer les nouveaux audios en arrière-plan
            foreach ($textsToGenerate as $text) {
                \App\Jobs\GenerateAudioCacheJob::dispatch($text);
            }

            return PackageControlleur::successResponse(
                $question,
                'Question modifiée avec succès!',
                ['count' => 1]
            );
        } catch (\Exception $e) {
            \Log::error('Erreur modification question', ['error' => $e->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la modification de la question.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function reorder(Request $request)
    {
        try {
            $questionsData = $request->validate([
                '*.id' => 'required|integer|exists:questions,id',
                '*.numero' => 'required|integer|min:1',
            ]);
            
            DB::beginTransaction();
            
            foreach ($questionsData as $question) {
                Question::where('id', $question['id'])->update(['numero' => $question['numero']]);
            }

            DB::commit();

            return PackageControlleur::successResponse(
                null,
                'Numéros des questions mis à jour avec succès.',
                ['count' => count($questionsData)]
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Erreurs de validation lors de la réorganisation', ['errors' => $e->errors()]);
            return PackageControlleur::errorResponse('Erreurs de validation', 422, $e->errors());
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour des numéros de questions', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur serveur interne lors de la mise à jour.\n'.$th->getMessage(), 500, []);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            \Log::error('Question non trouvée.');
            return PackageControlleur::errorResponse('Question non trouvée.', 404, []);
        }

        try {
            // 1. Réorganiser les numéros (J'ai nettoyé la double exécution qu'il y avait dans ton code)
            Question::where('thematique_id', $question->thematique_id)
                ->where('partie_id', $question->partie_id)
                ->where('numero', '>', $question->numero)
                ->decrement('numero');

            // 2. Supprimer les fichiers médias de l'intitulé (Images/Vidéos)
            if ($question->intitule_media_url && str_contains($question->intitule_media_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $question->intitule_media_url));
            }

            // 3. Supprimer les fichiers médias des options (Images/Vidéos)
            $options = json_decode($question->reponses, true);
            foreach ($options as $option) {
                if (isset($option['mediaUrl']) && str_contains($option['mediaUrl'], '/storage/')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $option['mediaUrl']));
                }
            }

            // 4. Extraire les textes pour supprimer intelligemment les audios
            $textsToDelete = $this->extractTextsFromQuestion($question);
            
            // 5. Supprimer le modèle en base de données
            $question->delete();

            // 6. Procéder au nettoyage des audios MAINTENANT que la question n'existe plus en base
            // (On passe 0 en paramètre car la question est déjà supprimée, elle ne faussera pas la recherche)
            foreach ($textsToDelete as $text) {
                $this->safeDeleteAudio($text, 0);
            }

            // Récupérer la nouvelle liste pour la renvoyer au frontend (si tu en as besoin)
            $data = Question::where('thematique_id', $question->thematique_id)
                ->where('partie_id', $question->partie_id)
                ->orderBy('numero')
                ->get();

            return PackageControlleur::successResponse(
                $data,
                'Question supprimée et numéros réorganisés avec succès!',
                ['count' => 0]
            );

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de la question.', ['error' => $e->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la suppression de la question : '.$e->getMessage());
        }
    }

    /**
     * Extrait les textes et lance les Jobs de génération audio en arrière-plan.
     */
    private function precacheQuestionAudios($intituleText, $intituleMediaDescription, array $options)
    {
        $textsToSynthesize = [];

        // 1. Récupérer le texte de l'intitulé ou sa description média
        if (!empty($intituleText)) {
            $textsToSynthesize[] = $intituleText;
        } elseif (!empty($intituleMediaDescription)) {
            $textsToSynthesize[] = $intituleMediaDescription;
        }

        // 2. Récupérer les textes ou descriptions médias des options
        foreach ($options as $option) {
            if (!empty($option['text'])) {
                $textsToSynthesize[] = $option['text'];
            } elseif (!empty($option['mediaDescription'])) {
                $textsToSynthesize[] = $option['mediaDescription'];
            }
        }

        // 3. Nettoyer le tableau (supprimer les chaînes vides et les doublons)
        $textsToSynthesize = array_unique(array_filter($textsToSynthesize));

        // 4. Envoyer chaque texte unique dans la file d'attente
        foreach ($textsToSynthesize as $text) {
            GenerateAudioCacheJob::dispatch($text);
        }
    }

    /**
     * Extrait tous les textes pertinents pour l'audio d'une question.
     */
    private function extractTextsFromQuestion($question): array
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
     * Supprime l'audio uniquement s'il n'est utilisé par aucune autre question.
     */
    private function safeDeleteAudio(string $text, int $excludeQuestionId = 0)
    {
        // On cherche si ce texte précis est utilisé dans une AUTRE question
        $isUsedElsewhere = \App\Models\Question::where('id', '!=', $excludeQuestionId)
            ->where(function($query) use ($text) {
                $query->where('intitule_text', $text)
                      ->orWhere('intitule_media_description', $text)
                      // Recherche basique dans le JSON des réponses
                      ->orWhere('reponses', 'like', '%' . $text . '%'); 
            })->exists();

        // S'il n'est utilisé nulle part ailleurs, on peut supprimer le fichier
        if (!$isUsedElsewhere) {
            $filePath = 'audio/' . md5($text) . '.mp3';
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
        }
    }

}
