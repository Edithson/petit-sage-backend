<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ResponseHelper;
use App\Models\BadgeUser;

class BadgeController extends Controller
{
    /**
     * Affiche une liste de tous les badges.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $badges = Badge::with('thematique')->get(); // Charge la thématique associée
            return ResponseHelper::successResponse(
                $badges,
                'Liste des badges récupérée avec succès',
                ['count' => $badges->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération badges', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la récupération des badges : ' . $th->getMessage());
        }
    }

    /**
     * Enregistre un nouveau badge.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'emoji' => 'nullable|string|max:255',
                'thematique_id' => 'required|exists:thematiques,id',
                'nbr_min_point' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, $validator->errors()->toArray());
            }

            $badge = Badge::create($request->all());
            $badge = Badge::with(['thematique', 'users:id,name'])->find($badge->id);

            return ResponseHelper::successResponse(
                $badge,
                'Badge créé avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur création du badge', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la création du badge : '.$th->getMessage());
        }
    }

    /**
     * Affiche les détails d'un badge spécifique, y compris les utilisateurs l'ayant obtenu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Charge le badge avec sa thématique et les utilisateurs qui l'ont obtenu
            $badge = Badge::with(['thematique', 'users:id,name'])->find($id);
            if (!$badge) {
                return ResponseHelper::errorResponse('Badge non trouvé.', 404);
            }
            // Utiliser la relation users() pour charger tous les utilisateurs associés
            $users = $badge->users()->withTrashed()->get();

            // Compter le nombre de profil ayant obtenue le badge
            $nbr_profil = BadgeUser::where('badge_id', $id)->count();

            $data = [
                'badge' => $badge,
                'users' => $users,
                'nbr_profil' => $nbr_profil
            ];

            return ResponseHelper::successResponse(
                $data,
                'Badge récupéré avec succès',
                ['count' => count($data)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération badge', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la récupération du badge : '.$th->getMessage());
        }
    }

    /**
     * Met à jour un badge existant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $badge = Badge::find($id);
            if (!$badge) {
                return ResponseHelper::errorResponse('Badge non trouvé.', 404);
            }

            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'emoji' => 'nullable|string|max:255',
                'thematique_id' => 'required|exists:thematiques,id',
                'nbr_min_point' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return ResponseHelper::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, $validator->errors()->toArray());
            }

            $badge->update($request->all());
            $badge = Badge::with(['thematique', 'users:id,name'])->find($badge->id);

            return ResponseHelper::successResponse(
                $badge,
                'Badge mis à jour avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur mise à jour du badge', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la mise à jour du badge : '.$th->getMessage());
        }
    }

    /**
     * Supprime un badge (soft delete).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return ResponseHelper::errorResponse('Accès non autorisé.', 403);
            }
            $badge = Badge::find($id);
            if (!$badge) {
                return ResponseHelper::errorResponse('Badge non trouvé.', 404);
            }
            $badge->delete(); // Soft delete
            return ResponseHelper::successResponse(
                null,
                'Badge supprimé avec succès.',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur suppression badge', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la suppression du badge : '.$th->getMessage());
        }
    }
}
