<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\MailService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Gère la connexion de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Régénère la session pour éviter les fixations de session
            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'message' => 'Connexion réussie.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type_id' => $user->type_id,
                    'profil' => $user->profil,
                ]
            ]);
        }

        return response()->json(['message' => 'Identifiants incorrects.'], 401);

    }

    /**
     * Gère la première étape de l'inscription : envoi du code de vérification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation : '.$validator->errors(), 'errors' => $validator->errors()], 422);
        }

        // Générer un code de vérification à 6 chiffres
        $verificationCode = random_int(100000, 999999);

        // Stocker le code de vérification temporairement
        PasswordResetToken::updateOrCreate(
            ['email' => $request->email],
            ['token' => $verificationCode, 'created_at' => now()]
        );

        // Envoie du mail
        $mailService = new MailService;
        $mailService->sendVerificationCode($request->email, $verificationCode);

        return response()->json([
            'message' => 'Code de vérification généré. Veuillez vérifier votre email.',
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password), // Hacher le mot de passe pour le passer à l'étape suivante
            'telephone' => $request->telephone,
        ], 200);
    }

    /**
     * Gère la deuxième étape de l'inscription : vérification du code et création de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
            'name' => 'required|string|max:255',
            'password' => 'required|string', // Le mot de passe haché de l'étape précédente
            'telephone' => 'nullable|string|max:20',
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

        // Supprimer le token après vérification réussie
        $storedToken->delete();

        // Générer un code unique pour l'utilisateur
        $userCode = $this->generateUniqueUserCode();

        // Créer l'utilisateur
        $user = User::create([
            'code' => $userCode,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Le mot de passe est déjà haché
            'telephone' => $request->telephone,
            'email_verified_at' => now(),
            'type_id' => 1, // Par défaut, un nouvel utilisateur est un apprenti (type_id = 1)
        ]);

        // Générer le token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type_id' => $user->type_id,
                'profil' => $user->profil,
            ]
        ], 201);
    }

    /**
     * Gère la demande de réinitialisation de mot de passe (envoi du code).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        // Générer un code de réinitialisation à 6 chiffres
        $resetCode = random_int(100000, 999999);

        // Stocker le code de réinitialisation temporairement
        PasswordResetToken::updateOrCreate(
            ['email' => $request->email],
            ['token' => $resetCode, 'created_at' => now()]
        );

        // Envoie du mail
        $mailService = new MailService;
        $mailService->sendVerificationCode($request->email, $resetCode);
        return response()->json([
            'message' => 'Un code de réinitialisation a été envoyé à votre email.',
            'email' => $request->email,
        ], 200);
    }

    /**
     * Gère la réinitialisation du mot de passe avec le code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reset_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erreurs de validation', 'errors' => $validator->errors()], 422);
        }

        $storedToken = PasswordResetToken::where('email', $request->email)
                                         ->where('token', $request->reset_code)
                                         ->first();

        if (!$storedToken || $storedToken->created_at->addMinutes(10)->isPast()) { // Code valide 10 minutes
            return response()->json(['message' => 'Code de réinitialisation invalide ou expiré.'], 400);
        }

        // Supprimer le token après vérification réussie
        $storedToken->delete();

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Votre mot de passe a été réinitialisé avec succès.'], 200);
    }

    /**
     * Déconnecte l'utilisateur (révoque le token Sanctum).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    /**
     * Récupère les informations de l'utilisateur authentifié.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'type_id' => $request->user()->type_id,
            'profil' => $request->user()->profil,
        ]);
    }

    /**
     * Génère un code utilisateur unique.
     *
     * @return string
     */
    private function generateUniqueUserCode(): string
    {
        do {
            $code = Str::uuid()->toString(); // Utilise UUID pour une grande unicité
        } while (User::where('code', $code)->exists());

        return $code;
    }
}
