<?php

namespace App\Http\Controllers;

use App\Models\Partie;
use App\Models\Question;
use App\Models\Thematique;
use App\Http\Requests\StorePartieRequest;
use App\Http\Requests\UpdatePartieRequest;
use App\Http\Controllers\PackageControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PartieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id = null)
    {
        try {
            if(isset($id)){
                $parties = Partie::where('thematique_id', $id)->orderBy('numero')->get();
            }else{
                $parties = Partie::orderBy('numero')->get();
            }
            return PackageControlleur::successResponse(
                $parties,
                'Liste des parties récupérée avec succès',
                ['count' => $parties->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération parties', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des parties : '.$th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartieRequest $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'thematique_id' => 'required|exists:thematiques,id',
            ]);

            if ($validator->fails()) {
                \Log::error('Erreurs de validation', ['error' => $validator->errors()]);
                return PackageControlleur::errorResponse('Erreurs de validation'.$validator->errors(), 422);
            }

            $data = $request->only(['name', 'description', 'thematique_id']);

            $num_partie = 1;
            $highestNumero = Partie::where('thematique_id', $data['thematique_id'])->max('numero');
            $num_partie = ($highestNumero === null) ? 1 : $highestNumero + 1;
            $data['numero'] = $num_partie;

            $partie = Partie::create($data);

            // Génération des audios en arrière-plan
            $textsToGenerate = $this->extractTextsFromPartie($partie);
            foreach ($textsToGenerate as $text) {
                \App\Jobs\GenerateAudioCacheJob::dispatch($text);
            }

            return PackageControlleur::successResponse(
                $partie,
                'Partie crée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur création partie de jeu', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la création de la partie de jeu'.$th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $partie = Partie::find($id);
            if (!$partie) {
                \Log::error('Partie non trouvée.');
                return PackageControlleur::errorResponse('Partie non trouvée.', 404, []);
            }
            $niveau = $partie->thematique->niveau;
            $questions = Question::where('partie_id', $partie->id)
                ->where('thematique_id', $partie->thematique_id)
                ->orderBy('numero')
                ->get();
            $thematique = Thematique::where('id', $partie->thematique_id)->first();

            //regroupement de données
            $data = [
                'partie' => $partie,
                'questions' => $questions,
                'thematique' => $thematique,
                'niveau' => $niveau
            ];
            return PackageControlleur::successResponse(
                $data,
                'Liste des parties récupérée avec succès',
                ['count' => count($data)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération parties', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des parties : '.$th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $partie = Partie::find($id);
            if (!$partie) {
                \Log::error('Partie non trouvée.');
                return PackageControlleur::errorResponse('Partie non trouvée.', 404, []);
            }
            return PackageControlleur::successResponse(
                $partie,
                'Données de la partie',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération partie', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération de la partie : '.$th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartieRequest $request, $id)
    {
        try {
            $partie = Partie::find($id);
            if (!$partie) {
                \Log::error('Partie non trouvée.');
                return PackageControlleur::errorResponse('Partie non trouvée.', 404, []);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                \Log::error('Erreurs de validation', ['error' => $validator->errors()]);
                return PackageControlleur::errorResponse('Erreurs de validation'.$validator->errors(), 422);
            }

            $data = $request->only(['name', 'description']);

            // 1. Capturer les textes AVANT la mise à jour
            $oldTexts = $this->extractTextsFromPartie($partie);

            // Mise à jour en base
            $partie->update($data);

            // 2. Capturer les textes APRÈS la mise à jour
            $newTexts = $this->extractTextsFromPartie($partie);

            // 3. Calculer les différences
            $textsToDelete = array_diff($oldTexts, $newTexts);
            $textsToGenerate = array_diff($newTexts, $oldTexts);

            // 4. Nettoyer les anciens audios inutilisés
            foreach ($textsToDelete as $text) {
                $this->safeDeleteAudio($text, $partie->id);
            }

            // 5. Lancer la génération des nouveaux audios
            foreach ($textsToGenerate as $text) {
                \App\Jobs\GenerateAudioCacheJob::dispatch($text);
            }

            return PackageControlleur::successResponse(
                $partie,
                'Partie Mise à jour avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur mise à jour partie de jeu', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la mise à jour de la partie de jeu'.$th->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        try {
            // Validez que la requête contient un tableau de parties
            $validatedData = $request->validate([
                '*.id' => 'required|exists:parties,id',
                '*.numero' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            foreach ($validatedData as $partieData) {
                Partie::where('id', $partieData['id'])->update(['numero' => $partieData['numero']]);
            }

            DB::commit();

            return PackageControlleur::successResponse(
                null,
                'Numéros de parties mis à jour avec succès.',
                ['count' => count($validatedData)]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Erreurs de validation lors de la réorganisation', ['error' => $e->errors()]);
            return PackageControlleur::errorResponse('Erreurs de validation', 422, $e->errors());
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Erreur lors de la mise à jour des numéros de parties', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la mise à jour des numéros de parties', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            if (auth()->user()->type_id < 2) {
                \Log::error('Accès non autorisé.');
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }

            $partie = Partie::find($id);
            if (!$partie) {
                \Log::error('Partie non trouvée ou déjà suspendu.');
                return PackageControlleur::errorResponse('Partie non trouvée ou déjà suspendu.', 404);
            }

            // CORRECTION ARCHITECTURALE : Ne pas utiliser le delete() en masse
            // On récupère les questions et on les supprime via le contrôleur pour déclencher le nettoyage audio
            $questions = Question::where('partie_id', $partie->id)->get();
            $questionController = app(\App\Http\Controllers\QuestionController::class);
            foreach ($questions as $question) {
                // Appel interne à la méthode destroy du QuestionController que nous avons optimisée plus tôt
                $questionController->destroy($question->id);
            }

            // 1. Capturer les textes de la partie AVANT de la détruire
            $textsToDelete = $this->extractTextsFromPartie($partie);

            // 2. Mettre à jour le numéro des parties supérieures
            Partie::where('numero', '>', $partie->numero)
                ->where('thematique_id', $partie->thematique_id)
                ->update([
                    'numero' => \DB::raw('numero - 1')
                ]);

            // 3. Supprimer la partie
            $partie->delete();

            // 4. Nettoyer les audios de la partie
            foreach ($textsToDelete as $text) {
                $this->safeDeleteAudio($text, 0);
            }

            return PackageControlleur::successResponse(
                [],
                'Partie supprimée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur lors de la suppression partie de jeu', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la suppression de la partie de jeu'.$th->getMessage());
        }
    }

    /**
     * Extrait les textes (nom et description) d'une partie.
     */
    private function extractTextsFromPartie($partie): array
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

    /**
     * Supprime l'audio s'il n'est utilisé par aucune autre Partie ou Question.
     */
    private function safeDeleteAudio(string $text, int $excludePartieId = 0)
    {
        // 1. Vérifier si utilisé dans une AUTRE partie
        $isUsedInParties = \App\Models\Partie::where('id', '!=', $excludePartieId)
            ->where(function($query) use ($text) {
                $query->where('name', $text)
                      ->orWhere('description', $text);
            })->exists();

        // 2. Vérifier si utilisé dans une Question existante
        $isUsedInQuestions = \App\Models\Question::where('intitule_text', $text)
            ->orWhere('intitule_media_description', $text)
            ->orWhere('reponses', 'like', '%' . $text . '%')
            ->exists();

        // S'il n'est utilisé nulle part, on le supprime physiquement
        if (!$isUsedInParties && !$isUsedInQuestions) {
            $filePath = 'audio/' . md5($text) . '.mp3';
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
            }
        }
    }

}
