<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Niveau;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PackageControlleur;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Affiche la liste de tous les utilisateurs (y compris suspendus pour l'admin).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            // Récupérer tous les utilisateurs, y compris ceux qui sont soft-deleted avec leurs types respectifs
            $users = User::withTrashed()->with('type')->get();

            return PackageControlleur::successResponse(
                $users,
                'Liste des utilisateurs récupérée avec succès',
                ['count' => $users->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération utilisateurs', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des utilisateurs : ' . $th->getMessage());
        }
    }

    /**
     * Affiche les informations d'un utilisateur spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            if (auth()->user()->type_id < 2 && auth()->id() != $id) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $user = User::withTrashed()->with('type', 'niveau')->find($id); // Inclure les utilisateurs suspendus
            if (!$user) {
                return PackageControlleur::errorResponse('Utilisateur non trouvé.', 404);
            }
            //reccuperer toutes les questions enregistrée par un utilisateur et tout les badges qu'il a gagné
            $questions = Question::where('created_by', $user->id)->get();
            $badges = $user->badges()->withTrashed()->get(); //meme si le badge a été supprimer

            $data = [
                'user' => $user,
                'questions' => $questions,
                'badges' => $badges,
            ];
            return PackageControlleur::successResponse(
                $data,
                'Données de l\'utilisateur récupérées avec succès',
                ['count' => count($data)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération utilisateur', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération de l\'utilisateur : ' . $th->getMessage());
        }
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'sexe' => 'nullable|string|in:Masculin,Féminin,Autre',
                'age' => 'nullable|integer|min:0|max:150',
                'telephone' => 'nullable|string|max:20',
                'niveau_id' => 'nullable|integer|exists:niveaux,id',
                'type_id' => 'required|integer|in:1,2,3', // 1:Apprenti, 2:Admin, 3:SuperAdmin (ajuster selon vos rôles)
                'profil' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, ['errors' => $validator->errors()]);
            }

            $num_niveau = $request->niveau_id;
            if ($request->type_id > 2) {
                $num_niveau = Niveau::max('numero');
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->sexe = $request->sexe || null;
            $user->age = $request->age || null;
            $user->telephone = $request->telephone || null;
            $user->type_id = $request->type_id;
            $user->niveau_id = $num_niveau;
            $user->code = $this->generateUniqueUserCode(); // Générer un code unique

            // Mot de passe par défaut (pour le développement)
            $user->password = Hash::make('password');

            if ($request->hasFile('profil')) {
                $path = $request->file('profil')->store('profil', 'public');
                $user->profil = Storage::url($path);
            }

            $user->save();

            // Marquer l'email comme non vérifié pour qu'il le fasse à la première connexion
            $user->email_verified_at = null;
            $user->save();

            return PackageControlleur::successResponse(
                $user,
                'Utilisateur créé avec succès',
                ['count' => 1],
                201
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur ajout utilisateur', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de l\'ajout du l\'utilisateur : ' . $th->getMessage());
        }
    }

    /**
     * Met à jour les informations de profil d'un utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::withTrashed()->find($id); // Inclure les utilisateurs suspendus
            if (!$user) {
                return PackageControlleur::errorResponse('Utilisateur non trouvé!.', 404);
            }
            // Un utilisateur ne peut modifier que son propre profil, un admin peut modifier n'importe quel profil
            if (auth()->id() != $user->id && auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'sexe' => 'nullable|string|in:Masculin,Féminin,Autre',
                'age' => 'nullable|integer|min:0|max:150',
                'telephone' => 'nullable|string|max:20',
                'niveau_id' => 'nullable|integer|exists:niveaux,id',
                'type_id' => 'required|integer|in:1,2,3', // Type d'utilisateur, seuls les admins peuvent changer cela
                'profil' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, ['errors' => $validator->errors()]);
            }
            // Gérer l'upload de la photo de profil
            if ($request->hasFile('profil')) {
                if ($user->profil && Storage::disk('public')->exists(str_replace('/storage/', '', $user->profil))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $user->profil));
                }
                $path = $request->file('profil')->store('profil', 'public');
                $user->profil = Storage::url($path);
            } elseif ($request->input('profil_removed')) {
                if ($user->profil && Storage::disk('public')->exists(str_replace('/storage/', '', $user->profil))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $user->profil));
                }
                $user->profil = null;
            }

            // Si l'email a changé, marquer email_verified_at comme null.
            // La mise à jour de l'email et de email_verified_at sera finalisée par verifyEmailUpdate.
            if ($request->email !== $user->email) {
                $user->email_verified_at = null;
                // Ne pas mettre à jour l'email ici, il sera mis à jour via verifyEmailUpdate
            }

            $user->name = $request->name;
            $user->sexe = $request->sexe;
            $user->age = $request->age;
            $user->telephone = $request->telephone;
            $user->niveau_id = $request->niveau_id;

            // Seuls les admins peuvent changer le type_id d'un utilisateur
            if (auth()->user()->type_id >= 2) {
                $user->type_id = $request->type_id;
            }

            $user->save();

            // Si l'email a changé, mettre à jour l'email de l'utilisateur après le save
            // pour qu'il ne soit pas unique:users,email,id validé contre l'ancienne email
            if ($request->email !== $user->email && !User::where('email', $request->email)->exists()) {
                $user->email = $request->email;
                $user->save(); // Sauvegarder l'email après les autres champs
            }

            return PackageControlleur::successResponse(
                $user,
                'Utilisateur mise à jour avec succès',
                ['count' => 1],
                201
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur mise à jour utilisateur', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la mise à jour de l\'utilisateur : ' . $th->getMessage());
        }
    }

    /**
     * Envoie un code de vérification pour une nouvelle adresse email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email', // Nouvelle adresse email, doit être unique
            'old_email' => 'required|email|exists:users,email', // Ancien email pour s'assurer que l'utilisateur est légitime
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        // Vérifier que l'utilisateur authentifié correspond à l'ancien email
        if (auth()->user()->email !== $request->old_email) {
            return response()->json(['message' => 'Accès non autorisé. L\'ancien email ne correspond pas à l\'utilisateur connecté.'], 403);
        }

        // Générer un code de vérification à 6 chiffres
        $verificationCode = random_int(100000, 999999);

        // Stocker le code de vérification temporairement, lié à la NOUVELLE adresse email
        PasswordResetToken::updateOrCreate(
            ['email' => $request->email], // Utiliser la nouvelle email comme clé
            ['token' => $verificationCode, 'created_at' => now()]
        );

        // Envoie du mail
        $mailService = new MailService;
        $mailService->sendVerificationCode($request->email, $verificationCode);

        return response()->json([
            'message' => 'Code de vérification généré pour la nouvelle adresse email.',
            'email' => $request->email,
        ], 200);
    }

    /**
     * Vérifie le code et met à jour l'adresse email de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmailUpdate(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        // Vérifier si l'utilisateur authentifié est bien celui qu'il essaie de modifier
        if (auth()->id() != $user->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'verification_code' => 'required|string|size:6',
            'name' => 'required|string|max:255',
            'sexe' => 'nullable|string|in:Masculin,Féminin,Autre',
            'age' => 'nullable|integer|min:0|max:150',
            'telephone' => 'nullable|string|max:20',
            'profil_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        $storedToken = PasswordResetToken::where('email', $request->email)
                                         ->where('token', $request->verification_code)
                                         ->first();

        if (!$storedToken || $storedToken->created_at->addMinutes(10)->isPast()) { // Code valide 10 minutes
            return response()->json(['message' => 'Code de vérification invalide ou expiré.'], 400);
        }

        $storedToken->delete();

        $user->email = $request->email;
        $user->email_verified_at = now();

        $user->name = $request->name;
        $user->sexe = $request->sexe;
        $user->age = $request->age;
        $user->telephone = $request->telephone;
        $user->profil = $request->profil_url;
        $user->save();

        return response()->json([
            'message' => 'Adresse email vérifiée et profil mis à jour avec succès.',
            'user' => [
                'id' => $user->id,
                'code' => $user->code,
                'name' => $user->name,
                'email' => $user->email,
                'sexe' => $user->sexe,
                'age' => $user->age,
                'telephone' => $user->telephone,
                'profil' => $user->profil,
                'type_id' => $user->type_id,
                'email_verified_at' => $user->email_verified_at,
                'deleted_at' => $user->deleted_at,
            ]
        ], 200);
    }

    /**
     * Permet à un utilisateur de changer son mot de passe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            \Log::error('Utilisateur non trouvé ou non suspendu.');
            return PackageControlleur::errorResponse('Utilisateur non trouvé ou non suspendu.', 404);
        }

        if (auth()->id() != $user->id) {
            \Log::error('Accès non autorisé. Vous ne pouvez pas modifier un super administrateur.');
            return PackageControlleur::errorResponse('Accès non autorisé. Vous ne pouvez pas modifier un super administrateur.', 403);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            \Log::error('Erreur de validation : '.$validator->errors());
            return PackageControlleur::errorResponse('Erreur de validation : '.$validator->errors(), 422);
         }

        if (!Hash::check($request->current_password, $user->password)) {
            \Log::error('Le mot de passe actuel est incorrect.');
            return PackageControlleur::errorResponse(
                'Le mot de passe actuel est incorrect.',
                401
            );
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return PackageControlleur::successResponse(
            [],
            'Mot de passe mis à jour avec succès.'
        );
    }

    /**
     * Suspend le compte d'un utilisateur (soft delete).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function suspendAccount(Request $request, $id)
    {
        // Seuls les administrateurs ou l'utilisateur lui-même peuvent suspendre
        if (auth()->user()->type_id < 2 && auth()->id() != $id) {
            \Log::error('Accès non autorisé. Vous ne pouvez pas suspendre un super administrateur.');
            return PackageControlleur::errorResponse('Accès non autorisé. Vous ne pouvez pas suspendre un super administrateur.', 403);
        }

        $user = User::find($id); // Ne pas utiliser withTrashed() ici, car on veut suspendre un utilisateur actif

        if (!$user) {
            \Log::error('Utilisateur non trouvé ou déjà suspendu.');
            return PackageControlleur::errorResponse('Utilisateur non trouvé ou déjà suspendu.', 404);
        }

        // Si c'est l'utilisateur lui-même qui suspend, il doit fournir son mot de passe
        if (auth()->id() == $id) {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                \Log::error('Erreur de validation : '.$validator->errors());
                return PackageControlleur::errorResponse('Erreur de validation : '.$validator->errors(), 422);
            }

            if (!Hash::check($request->password, $user->password)) {
                \Log::error('Mot de passe incorrect. Impossible de suspendre le compte.');
                return PackageControlleur::errorResponse('Mot de passe incorrect. Impossible de suspendre le compte.', 401);
            }
            $user->tokens()->delete(); // Déconnecter l'utilisateur si c'est lui-même
        } else {
            // Si c'est un admin qui suspend un autre utilisateur, pas besoin de mot de passe
            // Mais l'admin ne peut pas suspendre un super admin
            if ($user->type_id === 3 && auth()->user()->type_id < 3) {
                \Log::error('Accès non autorisé. Vous ne pouvez pas suspendre un super administrateur.');
                return PackageControlleur::errorResponse('Accès non autorisé. Vous ne pouvez pas suspendre un super administrateur.', 403);
            }
        }

        $user->delete(); // Cela mettra à jour le champ `deleted_at`
        $user_data = User::onlyTrashed()->find($id);

        return PackageControlleur::successResponse(
            $user_data,
            'Compte suspendu avec succès.',
            ['count' => 1]
        );
    }

    /**
     * Restaure un compte utilisateur suspendu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        // Seuls les administrateurs (type_id > 1) peuvent restaurer des utilisateurs
        if (auth()->user()->type_id < 2) {
            \Log::error('Accès non autorisé. Vous ne pouvez pas restaurer un super administrateur.');
            return PackageControlleur::errorResponse('Accès non autorisé. Vous ne pouvez pas restaurer un super administrateur.', 403);
        }

        $user = User::onlyTrashed()->find($id); // Chercher uniquement dans les éléments soft-deleted

        if (!$user) {
            \Log::error('Utilisateur non trouvé ou non suspendu.');
            return PackageControlleur::errorResponse('Utilisateur non trouvé ou non suspendu.', 404);
        }

        $user->restore(); // Restaure l'utilisateur

        return PackageControlleur::successResponse(
            $user,
            'Compte restauré avec succès.',
            ['count' => 1]
        );
    }

    /**
     * Génère un code utilisateur unique.
     *
     * @return string
     */
    protected function generateUniqueUserCode()
    {
        do {
            $code = Str::random(8); // Génère une chaîne aléatoire de 8 caractères
        } while (User::where('code', $code)->exists());

        return $code;
    }
}