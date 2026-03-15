<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Badge;
use App\Models\badge_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PackageControlleur;

class BadgeUsersController extends Controller
{
    /**
     * Affiche une liste de toutes les associations badge-utilisateur.
     * Peut être utile pour une vue d'ensemble côté admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id = null)
    {
        if (!auth()->user()) {
            return PackageControlleur::errorResponse('Accès non autorisé.', 403);
        }

        $user = auth()->user();

        // Ajout des colonnes essentielles de badge_users (solution du problème précédent)
        $selectColumns = [
            'id',
            'user_id',
            'badge_id',
            'profil_id',
            'created_at',
            'updated_at'
        ];

        // Définition de l'Eager Loading
        $relations = [
            'user:id,name',

            // MODIFICATION ICI : Utilisation d'une closure et de withTrashed()
            'badge' => function ($query) {
                $query->withTrashed()->select('id', 'titre', 'emoji', 'description');
            }
        ];

        $query = badge_users::select($selectColumns)->with($relations);

        if (isset($id) && !empty($id)) {
            $badgeUsers = $query
                ->where('user_id', $user->id)
                ->where('profil_id', $id)
                ->get();
        } else {
            $badgeUsers = $query
            ->where('user_id', $user->id)
            ->where('profil_id', null)
            ->get();
        }

        return PackageControlleur::successResponse(
            $badgeUsers,
            'Données de badges récupérés avec succès',
            ['count' => $badgeUsers->count()]
        );
    }

    /**
     * Attache un badge à un utilisateur.
     * Utile si un administrateur veut attribuer manuellement un badge.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        $badge = Badge::find($request->badge_id);

        // Vérifier si le badge n'est pas déjà attaché pour éviter les doublons
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id);
            return response()->json(['message' => 'Badge attaché avec succès.'], 201);
        }

        return response()->json(['message' => 'Badge déjà attaché à cet utilisateur.'], 409);
    }

    /**
     * Détache un badge d'un utilisateur.
     * Utile si un administrateur veut retirer manuellement un badge.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detach(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        $badge = Badge::find($request->badge_id);

        if ($user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->detach($badge->id);
            return response()->json(['message' => 'Badge détaché avec succès.'], 200);
        }

        return response()->json(['message' => 'Cet utilisateur ne possède pas ce badge.'], 404);
    }
}

