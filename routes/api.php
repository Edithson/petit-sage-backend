<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TTSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\PartieController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\BadgeUsersController;
use App\Http\Controllers\ElevenLabsController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ThematiqueController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ContactController;

Route::get('/evaluations/user/{id?}', [EvaluationController::class, 'getEvalUser']); //reccuperer les données d'évaluation d'un compte utilisateur ou d'un sous compte

// attribution des numéros d'ordre aux questions
Route::get('/questions/assign-order-numbers', [QuestionController::class, 'giveQuestionNumber']);
Route::put('/questions/reorder', [QuestionController::class, 'reorder']);

//synthèse vocale
Route::post('/tts', [TTSController::class, 'generate']);
Route::post('/synthesize-speech', [ElevenLabsController::class, 'synthesize']);

Route::put('/parties/reorder', [PartieController::class, 'reorder']);

// Routes d'authentification (publiques)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes pour les Niveaux (NOUVELLES ROUTES)
Route::put('/thematiques/{id}', [ThematiqueController::class, 'update']);

// Routes pour les paramètres
Route::get('/settings', [SettingController::class, 'get_setting']);

// Routes pour les contacts
Route::post('/contacts', [ContactController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

    // Changement des paramètres
    Route::post('/settings', [SettingController::class, 'set_setting']);

    // Routes pour les contacts
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/{contact}', [ContactController::class, 'show']);
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
    Route::get('/contacts/unread-count', [ContactController::class, 'unreadCount']);
    
    // Routes pour les questions
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);
    Route::put('/questions/{id}', [QuestionController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);
    Route::get('/questions/thematique/{id}', [QuestionController::class, 'showQuestionThematics']);

    Route::get('/thematiques/get_one/{id}', [ThematiqueController::class, 'get_one']);
    Route::get('/thematiques/show/{id}', [ThematiqueController::class, 'show']);


    // Routes pour les thématiques
    Route::get('/thematiques/playable', [ThematiqueController::class, 'index_playable']);
    Route::get('/thematiques/get_main_theme', [ThematiqueController::class, 'get_main_theme']);
    Route::get('/thematiques/{id?}', [ThematiqueController::class, 'index']);
    Route::post('/thematiques', [ThematiqueController::class, 'store']);
    Route::delete('/thematiques/{id}', [ThematiqueController::class, 'destroy']);

    Route::get('/niveaux', [NiveauController::class, 'index']); // Liste des niveaux
    Route::post('/niveaux', [NiveauController::class, 'store']); // Créer un niveau
    Route::get('/niveaux/{id}', [NiveauController::class, 'show']); // Afficher un niveau
    Route::put('/niveaux/{id}', [NiveauController::class, 'update']); // Modifier un niveau
    Route::delete('/niveaux/{id}', [NiveauController::class, 'destroy']); // Supprimer (suspendre) un niveau
    Route::post('/niveaux/{id}/restore', [NiveauController::class, 'restore']); // Restaurer un niveau

    // les parties
    Route::get('/parties/{id?}', [PartieController::class, 'index']);
    Route::post('/parties', [PartieController::class, 'store']);
    Route::get('/parties/show/{id}', [PartieController::class, 'show']);
    Route::get('/parties/edit/{id}', [PartieController::class, 'edit']);
    Route::put('/parties/{id}', [PartieController::class, 'update']);
    Route::delete('/parties/{id}', [PartieController::class, 'destroy']);

    // Routes pour les Badges
    Route::get('/badges', [BadgeController::class, 'index']);
    Route::post('/badges', [BadgeController::class, 'store']);
    Route::get('/badges/{id}', [BadgeController::class, 'show']);
    Route::put('/badges/{id}', [BadgeController::class, 'update']);
    Route::delete('/badges/{id}', [BadgeController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes pour les Associations Badge-Utilisateur (si vous voulez des actions directes sur la pivot)
    Route::get('/badgeusers/{id?}', [BadgeUsersController::class, 'index']);
    Route::post('/badgeusers/attach', [BadgeUsersController::class, 'attach']);
    Route::post('/badgeusers/detach', [BadgeUsersController::class, 'detach']);

    // les profils
    Route::get('/profil', [ProfilController::class, 'get_profils_user']);
    Route::post('/profil', [ProfilController::class, 'store']);
    Route::get('/profil/show/{id?}', [ProfilController::class, 'show']);
    Route::get('/profil/edit/{id}', [ProfilController::class, 'edit']);
    Route::put('/profil/{id}', [ProfilController::class, 'update']);
    Route::delete('/profil/{id}', [ProfilController::class, 'destroy']);
    Route::get('/profil/toggle-state/{id}', [ProfilController::class, 'toggleStatus']);
    Route::post('/profil/login', [ProfilController::class, 'loginProfil']);

    // Routes pour les Evaluations
    Route::post('/evaluations', [EvaluationController::class, 'store']);
    Route::get('/evaluations/{id}', [EvaluationController::class, 'show']); // Pour consulter une évaluation spécifique
    Route::get('/evaluations', [EvaluationController::class, 'index']); // Pour lister toutes les évaluations (admin)

    // Routes de gestion de profil personnel (déjà existantes)
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword']);
    Route::post('/users/{id}/suspend', [UserController::class, 'suspendAccount']); // Pour la suspension personnelle

    // Routes de gestion des utilisateurs par l'administrateur
    Route::get('/users', [UserController::class, 'index']); // Liste de tous les utilisateurs
    Route::post('/users', [UserController::class, 'store']); // Créer un utilisateur
    Route::post('/users/{id}/restore', [UserController::class, 'restore']); // Restaurer un utilisateur
    // La route PUT /users/{id} est déjà utilisée pour la mise à jour du profil.
    // La route POST /users/{id}/suspend est utilisée pour la suspension personnelle et par l'admin.

    // Routes pour la vérification d'email lors de la mise à jour
    Route::post('/users/send-email-verification-code', [UserController::class, 'sendEmailVerificationCode']);
    Route::post('/users/{id}/verify-email-update', [UserController::class, 'verifyEmailUpdate']);

    Route::get('/types', [TypeController::class, 'index']);

    Route::get('/evaluations/startEvaluation/{id}', [EvaluationController::class, 'startEvaluation']); // Pour démarrer une évaluation
});

