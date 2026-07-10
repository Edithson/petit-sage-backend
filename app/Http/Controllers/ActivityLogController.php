<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\ResponseHelper;
use Illuminate\Support\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Get a paginated listing of activity logs with filters.
     */
    public function index(Request $request)
    {
        // Vérifier les droits (Admins ou Super Admins)
        $user = $request->user();
        if (!$user || $user->type_id < 2) {
            return ResponseHelper::errorResponse('Accès non autorisé.', 403);
        }

        try {
            $query = Activity::with('causer')->orderBy('created_at', 'desc');

            // Filtre par type de modèle (subject_type)
            if ($request->filled('subject_type')) {
                $subjectType = $request->subject_type;
                if (!str_starts_with($subjectType, 'App\\Models\\') && $subjectType !== 'System' && $subjectType !== 'Auth') {
                    $subjectType = 'App\\Models\\' . ucfirst($subjectType);
                }
                $query->where('subject_type', $subjectType);
            }

            // Filtre par catégorie de log (log_name)
            if ($request->filled('log_name')) {
                $query->where('log_name', $request->log_name);
            }

            // Filtre par description / action (created, updated, deleted, login, etc.)
            if ($request->filled('action')) {
                $query->where('description', $request->action);
            }

            // Filtre par auteur / utilisateur déclencheur (causer_id)
            if ($request->filled('causer_id')) {
                $query->where('causer_id', $request->causer_id);
            }

            // Filtre par adresse IP (stocké dans les properties JSON de Spatie)
            if ($request->filled('ip')) {
                $query->where('properties->ip', $request->ip);
            }

            // Filtres temporels (date de début et date de fin)
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
            }

            $perPage = $request->get('per_page', 15);
            $logs = $query->paginate($perPage);

            return ResponseHelper::successResponse(
                $logs,
                'Journal des activités récupéré avec succès.'
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération journal des activités', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la récupération du journal des activités : ' . $th->getMessage());
        }
    }

    /**
     * Get activity logs for the authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return ResponseHelper::errorResponse('Non authentifié.', 401);
        }

        try {
            $query = Activity::where('causer_type', 'App\Models\User')
                ->where('causer_id', $user->id)
                ->orderBy('created_at', 'desc');

            // Filtrer par profil si profil_id est fourni
            if ($request->filled('profil_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('subject_type', 'App\Models\Profil')
                      ->where('subject_id', $request->profil_id);
                });
            }

            // Filtrer par type d'action si besoin (ex: login, logout, etc.)
            if ($request->filled('action')) {
                $query->where('description', $request->action);
            }

            $perPage = $request->get('per_page', 15);
            $logs = $query->paginate($perPage);

            return ResponseHelper::successResponse(
                $logs,
                'Vos activités récupérées avec succès.'
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération journal des activités utilisateur', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la récupération de vos activités : ' . $th->getMessage());
        }
    }
}
