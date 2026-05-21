<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use App\Models\Contact;
use App\Models\Evaluation;
use App\Models\Niveau;
use App\Models\Question;
use App\Models\Partie;
use App\Models\Profil;
use App\Models\Thematique;
use App\Models\badge_users;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PackageControlleur;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche les statistiques globales du tableau de bord.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // 1. Les indicateurs clés (KPIs)
            $kpis = [
                'total_users' => User::count(),
                'total_evaluations' => Evaluation::count(),
                'total_badges' => badge_users::count(), // Badges débloqués par les apprenants
                'unread_contacts' => Contact::unread()->count(),
            ];

            // 2. Répartition des utilisateurs par Niveau (pour le graphique)
            // On suppose que la table niveaux a une colonne 'nom' ou 'titre'
            $usersPerNiveau = Niveau::withCount('users')->get()->map(function ($niveau) {
                return [
                    'name' => $niveau->nom ?? 'Niveau ' . $niveau->id, 
                    'utilisateurs' => $niveau->users_count
                ];
            });

            // 3. Activité récente : Les 5 dernières évaluations
            $recentEvaluations = Evaluation::with(['user', 'thematique', 'partie'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($eval) {
                    return [
                        'id' => $eval->id,
                        'user_name' => $eval->user->name ?? 'Utilisateur inconnu',
                        'thematique' => $eval->thematique->nom ?? 'N/A',
                        'partie' => $eval->partie->nom ?? 'N/A',
                        'score' => $eval->score ?? 0, // Assure-toi d'avoir une colonne score si applicable
                        'date' => $eval->created_at->diffForHumans()
                    ];
                });

            $data = [
                'kpis' => $kpis,
                'chartData' => $usersPerNiveau,
                'recentActivity' => $recentEvaluations
            ];

            return PackageControlleur::successResponse(
                $data,
                'Données statistiques récupérées avec succès'
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération données Dashboard', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des données : ' . $th->getMessage());
        }
    }
}