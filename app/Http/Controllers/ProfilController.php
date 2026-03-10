<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use App\Models\Badge;
use App\Models\Niveau;
use App\Models\Profil;
use App\Models\Evaluation;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProfilRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateProfilRequest;
use App\Http\Controllers\PackageControlleur;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // reccuperer l'ensemble des profiles
    public function index()
    {
        try {
            $profiles = Profil::with('user', 'niveau')->orderBy('created_at', 'desc')->get();
            return PackageControlleur::successResponse(
                $profiles,
                'Liste des profiles récupérée avec succès',
                ['count' => $profiles->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération des profiles joueurs', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des profiles joueurs : '.$th->getMessage());
        }
    }

    //reccuperer les profiles d'un utilisateur en partucilier
    public function get_profils_user()
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $profiles = Profil::with('niveau')->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
            return PackageControlleur::successResponse(
                $profiles,
                'Liste des profiles récupérée avec succès',
                ['count' => $profiles->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération des profiles joueurs', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des profiles joueurs : '.$th->getMessage());
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
    public function store(Request $request)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'sexe' => 'nullable|string|in:Masculin,Féminin,Autre',
                'age' => 'nullable|integer|min:0|max:150',
                'niveau_id' => 'nullable|integer|exists:niveaux,id',
                'profil' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, ['errors' => $validator->errors()]);
            }

            $data = $request->only(['name', 'sexe', 'age', 'niveau_id']);
            $data['code'] = PackageControlleur::generateUniqueUserCode(); // Générer un code unique
            $data['user_id'] = auth()->user()->id;
            // Mot de passe par défaut (pour le développement)
            $data['password'] = PackageControlleur::crypterChaine('1234');

            if ($request->hasFile('profil')) {
                $path = $request->file('profil')->store('profil', 'public');
                $data['profil'] = Storage::url($path);
            }
            $data['is_active'] = true;

            $profil = Profil::create($data);

            return PackageControlleur::successResponse(
                $profil,
                'Profil créé avec succès',
                ['count' => 1],
                201
            );
        } catch (\Throwable $th) {
            Log::error('Erreur ajout profil', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de l\'ajout du profil : ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Vérifier l'authentification
            if (!auth()->check()) {
                return PackageControlleur::errorResponse('Non authentifié.', 401);
            }

            $profil = Profil::with(['user', 'niveau', 'badges'])
                ->where('id', $id)
                ->first();

            if (!$profil) {
                return PackageControlleur::errorResponse('Profil non trouvé.', 404);
            }

            // Vérification des autorisations
            if (auth()->user()->type_id < 2 && auth()->user()->id !== $profil->user_id) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }

            // Décryptage du mot de passe (si nécessaire - à éviter de retourner au front!)
            try {
                $decrypted = PackageControlleur::decrypterChaine($profil->password);
                $profil->password = $decrypted ?: "";
            } catch (\Throwable $th) {
                \Log::error('Erreur lors du décryptage du mot de passe : '.$th->getMessage(), ['error' => $th->getMessage()]);
                $profil->password = "";
            }

            // Récupération des évaluations
            $evaluations = Evaluation::with(['partie', 'thematique'])
                ->where('user_id', $profil->user_id)
                ->where('profil_id', $profil->id)
                ->get();

            $badges = [];

            $data = [
                'profil' => $profil,
                'evaluations' => $evaluations,
                'badges' => $badges,
            ];

            return PackageControlleur::successResponse(
                $data,
                'Profil récupéré avec succès',
                ['count' => 1],
                200
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur selection profil', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return PackageControlleur::errorResponse(
                'Erreur lors de la sélection du profil : ' . $th->getMessage(),
                500
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $profil = Profil::find($id);
            if (!$profil) {
                return PackageControlleur::errorResponse('Profil non trouvé.', 404);
            }
            if (auth()->user()->type_id < 2 || auth()->user()->id !== $profil->user_id) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            // Décryptage du mot de passe (si nécessaire - à éviter de retourner au front!)
            try {
                $decrypted = PackageControlleur::decrypterChaine($profil->password);
                $profil->password = $decrypted ?: "";
            } catch (\Throwable $th) {
                \Log::error('Erreur lors du décryptage du mot de passe : '.$th->getMessage(), ['error' => $th->getMessage()]);
                $profil->password = "";
            }
            return PackageControlleur::successResponse(
                $profil,
                'Profil mit à jour avec succès',
                ['count' => 1],
                200
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur selection profil', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la sélection du profil : ' . $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'sexe' => 'nullable|string|in:Masculin,Féminin,Autre',
                'age' => 'nullable|integer|min:0|max:150',
                'niveau_id' => 'nullable|integer|exists:niveaux,id',
                'profil' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'remove_profil' => 'nullable|boolean',
                'password' => 'nullable|string|max:10',
            ]);

            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, ['errors' => $validator->errors()]);
            }

            $data = $request->only(['name', 'sexe', 'age', 'niveau_id', 'password']);
            $profil = Profil::find($id);
            if ($request->filled('password')) {
                $data['password'] = PackageControlleur::crypterChaine($request->password);
            } else {
                unset($data['password']);
            }

            if ($request->hasFile('profil')) {
                // Si un ancien profil existe, le supprimer
                if ($profil->profil && Storage::disk('public')->exists(str_replace('/storage/', '', $profil->profil))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $profil->profil));
                }
                // Enregistrer le nouveau fichier
                $path = $request->file('profil')->store('profil', 'public');
                $data['profil'] = Storage::url($path);
            } elseif ($request->input('remove_profil') && $request->input('remove_profil') == 1) {
                // Supprimer l'ancien fichier s'il existe
                if ($profil->profil && Storage::disk('public')->exists(str_replace('/storage/', '', $profil->profil))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $profil->profil));
                }
                $data['profil'] = null;
            }

            $profil->update($data);

            return PackageControlleur::successResponse(
                $profil,
                'Profil mit à jour avec succès',
                ['count' => 1],
                201
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur mise à jour profil', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la mise à jour du profil : ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $profile = Profil::find($id);
            if (!$profile) {
                \Log::error('Profile non trouvée ou déjà suspendu.');
                return PackageControlleur::errorResponse('Profile non trouvée ou déjà suspendu.', 404);
            }
            $profile->delete();

            return PackageControlleur::successResponse(
                [],
                'Profile supprimée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur suppression profil', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la suppression du profil : ' . $th->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $profile = Profil::find($id);
            if (!$profile) {
                return PackageControlleur::errorResponse('Profile non trouvée.', 404);
            }
            if (auth()->user()->type_id < 2 || auth()->user()->id !== $profile->user_id) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $profile->is_active = !$profile->is_active;
            $profile->save();

            return PackageControlleur::successResponse(
                $profile,
                'Status du profil mis à jour avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur mise à jour status profil', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la mise à jour du status du profil : ' . $th->getMessage());
        }
    }

    // Fonction de connexion d'un profil joueur
    public function loginProfil(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'profil_id' => 'required|integer|exists:profils,id',
                'password' => 'nullable|string|max:10',
            ]);
            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, ['errors' => $validator->errors()]);
            }
            $profil = Profil::find($request->profil_id);
            if (!$profil) {
                return PackageControlleur::errorResponse('Profil non trouvé.', 404);
            }
            if (auth()->user()->type_id < 2 || auth()->user()->id !== $profil->user_id) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            if ( isset($profil->password) && !empty($profil->password)) {
                if ($request->password) {
                    if ($request->password !== PackageControlleur::decrypterChaine($profil->password)) {
                        return PackageControlleur::errorResponse('Mot de passe incorrect.', 401);
                    }
                }else {
                    // Si aucun mot de passe n'est fourni, on utilise le mot de passe par défaut
                    if (PackageControlleur::decrypterChaine($profil->password) !== '1234') {
                        return PackageControlleur::errorResponse('Mot de passe requis.', 401);
                    }
                }
            }

            return PackageControlleur::successResponse(
                $profil,
                'Connexion réussie',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur connexion profil', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la connexion du profil : ' . $th->getMessage());
        }
    }

}
