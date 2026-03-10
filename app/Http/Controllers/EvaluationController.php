<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Badge;
use App\Models\Partie;
use App\Models\Question;
use App\Models\Evaluation;
use App\Models\Thematique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PackageControlleur;
use App\Models\User; // Pour accéder au modèle User
use Illuminate\Support\Facades\DB; // Pour les agrégations de score

class EvaluationController extends Controller
{
    /**
     * Enregistre une nouvelle évaluation et vérifie l'obtention de badges.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                \Log::error('Token invalide');
                return PackageControlleur::errorResponse('Token invalide', 401);
            }

            $validator = Validator::make($request->all(), [
                'partie_id' => 'required|exists:parties,id',
                'score' => 'required|integer|min:0',
                'questions' => 'nullable|string',
                'temps' => 'nullable|string',
                'drawing_data' => 'nullable|string', // Base64 de l'image ou JSON
                'profil_id' => 'nullable|exists:profils,id',
                'max_score' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                \Log::error('Erreurs de validation', ['errors' => $validator->errors(), 'request_data' => $request->all()]);
                return PackageControlleur::errorResponse('Erreurs de validation : '.$validator->errors(), 422);
            }

            $partie = Partie::find($request->partie_id);

            // Décoder les questions si elles existent
            $questionsData = '[]';
            if ($request->has('questions') && !empty($request->questions)) {
                $decoded = json_decode($request->questions, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $questionsData = $request->questions;
                } else {
                    \Log::error('Format JSON invalide pour questions', ['questions' => $request->questions]);
                }
            }

            // Traitement des données de dessin
            $drawingPath = null;
            if ($request->has('drawing_data') && !empty($request->drawing_data)) {
                try {
                    $drawingPath = $this->saveDrawingImage($request->drawing_data, $user->id, $partie->id);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de la sauvegarde de l\'image de dessin', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'partie_id' => $partie->id
                    ]);
                    // On continue sans l'image si elle ne peut pas être sauvegardée
                }
            }
            $max_score = $request->has('max_score') ? $request->max_score : 10;
            $profil_id = $request->has('profil_id') ? $request->profil_id : null;

            // Créer l'évaluation
            $evaluation = Evaluation::create([
                'thematique_id' => $partie->thematique_id,
                'partie_id' => $partie->id,
                'user_id' => $user->id,
                'score' => $request->score,
                'question' => $questionsData,
                'temps' => $request->temps,
                'drawing_data' => $drawingPath, // Chemin vers l'image sauvegardée
                'profil_id' => $profil_id,
                'max_score' => $max_score,
            ]);

            $thematiqueId = $partie->thematique_id;
            $earnedBadges = [];

            if ($user) {
                $totalScoreForTheme = Evaluation::where('user_id', $user->id)
                                                ->where('thematique_id', $thematiqueId)
                                                ->sum('score');

                $badgesForTheme = Badge::where('thematique_id', $thematiqueId)->get();

                foreach ($badgesForTheme as $badge) {
                    if ($totalScoreForTheme >= $badge->nbr_min_point) {
                        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
                            $user->badges()->attach($badge->id);
                            $earnedBadges[] = $badge;
                        }
                    }
                }
            }

            $data = [
                'evaluation' => $evaluation,
                'badge' => $earnedBadges,
            ];

            return PackageControlleur::successResponse(
                $data,
                'Partie sauvegardée avec succès',
                ['count' => count($earnedBadges)]
            );

        } catch (\Throwable $th) {
            \Log::error('Erreur lors de d\'enregistrement des données', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return PackageControlleur::errorResponse(
                'Erreur lors de d\'enregistrement des données : '.$th->getMessage(),
                500
            );
        }
    }

    /**
     * Sauvegarde l'image de dessin dans le storage public
     */
    private function saveDrawingImage($drawingData, $userId, $partieId)
    {
        // Si c'est du JSON, on le décode d'abord
        $decodedData = json_decode($drawingData, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedData['image'])) {
            $base64Image = $decodedData['image'];
        } else {
            $base64Image = $drawingData;
        }

        // Vérifier si c'est une image base64 valide
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            throw new Exception('Format d\'image invalide');
        }

        $imageType = $matches[1]; // png, jpg, etc.

        // Nettoyer la chaîne base64
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $imageData = base64_decode($base64Image);

        if ($imageData === false) {
            throw new \Exception('Impossible de décoder l\'image base64');
        }

        // Générer un nom de fichier unique
        $fileName = 'drawing_' . $userId . '_' . $partieId . '_' . time() . '.' . $imageType;

        // Définir le chemin de sauvegarde
        $directory = 'drawings/' . date('Y/m');
        $filePath = $directory . '/' . $fileName;

        // Sauvegarder le fichier dans le storage public
        $saved = Storage::disk('public')->put($filePath, $imageData);

        if (!$saved) {
            throw new \Exception('Impossible de sauvegarder l\'image');
        }

        // Retourner le chemin relatif pour la base de données
        return $filePath;
    }

    /**
     * Affiche les détails d'une évaluation spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $evaluation = Evaluation::with(['user:id,name', 'thematique:id,name'])->find($id);

        if (!$evaluation) {
            return response()->json(['message' => 'Évaluation non trouvée.'], 404);
        }

        return response()->json($evaluation);
    }

    /**
     * Affiche toutes les évaluations (pour l'admin).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $evaluations = Evaluation::with(['user:id,name', 'thematique:id,name'])->get();
        return response()->json($evaluations);
    }

    public function startEvaluation($id){
         //vérification de l'existence de la partie
        try {
            $partie = Partie::find($id);
            if (!$partie) {
                \Log::warning("Tentative d'accès à une partie inexistante: ID " . $id);
                return PackageControlleur::errorResponse('Partie non trouvée', 404);
            }
            $sousThematique = Thematique::find($partie->thematique_id);
            $thematique = [];
            if ($sousThematique && $sousThematique->parent_id) {
                $thematique = Thematique::find($sousThematique->parent_id);
            }
            $questions = Question::where('partie_id', $partie->id)->orderBy('numero')->limit(10)->get();
            //regroupement de données
            $data = [
                'partie' => $partie,
                'thematique' => $thematique,
                'sousThematique' => $sousThematique,
                'questions' => $questions
            ];
            return PackageControlleur::successResponse(
                $data,
                'Données d\'évaluation récupérées avec succès',
                ['count' => count($data)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération des données d\'écaluation : '.$th->getMessage(), ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des des données d\'écaluation : '.$th->getMessage());
        }
    }

    public function getEvalUser($id = null){
        return true;
        // if (!auth()->user()) {
        //     return PackageControlleur::errorResponse('Accès non autorisé.', 403);
        // }
        // $user = auth()->user();
        // return($user);

        try {
            if (isset($id) && !empty($id)) {
                $evaluations = Evaluation::with(['partie', 'thematique'])
                    // ->where('user_id', $user->id)
                    ->where('profil_id', $id)
                    ->get();
            }else{
                $evaluations = Evaluation::with(['partie', 'thematique'])
                    // ->where('user_id', $user->id)
                    ->get();
            }

            return PackageControlleur::successResponse(
                $evaluations,
                'Données d\'évaluation récupérés avec succès',
                ['count' => $evaluations->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur selection données d\'évaluation', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return PackageControlleur::errorResponse(
                'Erreur lors de la sélection des données d\'évaluation : ' . $th->getMessage(),
                500
            );
        }
    }
}
