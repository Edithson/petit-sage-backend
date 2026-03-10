<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Thematique;
use App\Models\User;
use App\Http\Controllers\PackageControlleur;

class NiveauController extends Controller
{
    /**
     * Affiche la liste de tous les niveaux, y compris les niveaux suspendus (soft-deleted).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $niveaux = Niveau::orderBy('numero', 'asc')->get();
            return PackageControlleur::successResponse(
                $niveaux,
                'Liste des niveaux récupérée avec succès',
                ['count' => $niveaux->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération niveaux', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des niveaux : ' . $th->getMessage());
        }

    }

    /**
     * Crée un nouveau niveau en respectant l'ordre séquentiel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Seuls les administrateurs (type_id > 1) peuvent créer des niveaux
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:niveaux,nom,NULL,id,deleted_at,NULL', // Unique pour les niveaux actifs
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation : ' . $validator->errors(), 422, ['errors' => $validator->errors()]);
            }

            // Trouver le numéro de niveau le plus élevé parmi les niveaux actifs
            $highestNumero = Niveau::max('numero');
            // Déterminer le numéro du nouveau niveau
            $newNumero = ($highestNumero === null) ? 1 : $highestNumero + 1;
            // Vérifier si un niveau avec ce numéro existe déjà (même s'il est soft-deleted)
            $existingNiveau = Niveau::where('numero', $newNumero)->first();
            if ($existingNiveau) {
                 // Ceci ne devrait pas arriver avec max('numero'), mais par sécurité
                 return PackageControlleur::errorResponse('Un niveau actif avec le numéro ' . $newNumero . ' existe déjà. Impossible de créer un doublon.', 409, ['numero_conflit' => $newNumero]);
            }

            $niveau = new Niveau();
            $niveau->numero = $newNumero;
            $niveau->nom = $request->nom;
            $niveau->description = $request->description;
            $niveau->save();

            return PackageControlleur::successResponse(
                $niveau,
                'Niveau créé avec succès',
                ['count' => 1],
                201
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur création niveaux', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la création du niveau : ' . $th->getMessage());
        }
    }

    /**
     * Affiche les informations d'un niveau spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Seuls les administrateurs (type_id > 1) peuvent accéder à cette fonction
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $niveau = Niveau::withTrashed()->find($id);
            if (!$niveau) {
                return PackageControlleur::errorResponse('Niveau non trouvé.', 404);
            }
            // selection des utilisateurs de ce niveau
            $utilisateurs = User::where('niveau_id', $niveau->id)->get();
            // sélection des thématiques de ce niveau
            $thematiques = Thematique::where('niveau_id', $niveau->id)->get();
            $data = [
                'niveau' => $niveau,
                'utilisateurs' => $utilisateurs,
                'thematiques' => $thematiques,
            ];
            return PackageControlleur::successResponse(
                $data,
                'Niveau récupéré avec succès',
                ['count' => count($data)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération niveaux', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération du niveau : '.$th->getMessage());
        }
    }

    /**
     * Met à jour les informations d'un niveau. Le numéro ne peut pas être modifié.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }
            $niveau = Niveau::find($id);
            if (!$niveau) {
                return PackageControlleur::errorResponse('Niveau non trouvé.', 404);
            }

            $validator = Validator::make($request->all(), [
                'nom' => ['required', 'string', 'max:255', Rule::unique('niveaux')->ignore($niveau->id, 'id')->whereNull('deleted_at')], // Unique pour les niveaux actifs, sauf celui-ci
                'description' => 'nullable|string|max:1000',
            ]);
            if ($validator->fails()) {
                return PackageControlleur::errorResponse('Erreurs de validation', 422, ['errors' => $validator->errors()]);
            }

            $niveau->nom = $request->nom;
            $niveau->description = $request->description;
            $niveau->save();

            return PackageControlleur::successResponse(
                $niveau,
                'Niveau mis à jour avec succès.',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur de mise à jour de niveau', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur de mise à jour de niveau : '.$th->getMessage());
        }
    }

    /**
     * Suspend (soft delete) un niveau. Seul le niveau le plus élevé peut être suspendu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            if (auth()->user()->type_id < 2) {
                return PackageControlleur::errorResponse('Accès non autorisé.', 403);
            }

            $niveau = Niveau::find($id); // Cherche seulement les niveaux actifs

            if (!$niveau) {
                return PackageControlleur::errorResponse('Niveau non trouvé ou déjà suspendu.', 404);
            }

            //vérifier si il s'agit du niveau 1
            if ($niveau->numero == 1) {
                return PackageControlleur::errorResponse('Impossible de supprimer le niveau de base.', 409);
            }

            // Faire dessendre (de moins 1) les niveaux supérieurs au niveau sur le point d'être supprimé
            Niveau::where('numero', '>', $niveau->numero)->get()->each(function ($supNiveau) {
                $supNiveau->numero -= 1;
                $supNiveau->save();
            });

            //Rétrogradé les thématiques, les sous-thématiques et les niveaux relatif au niveau immédiatement inférieurs
            Thematique::where('niveau_id', $niveau->numero)->update([
                'niveau_id' => $niveau->numero-1
            ]);
            User::where('niveau_id', $niveau->numero)->update([
                'niveau_id' => $niveau->numero-1
            ]);

            $niveau->delete();
            return PackageControlleur::successResponse(
                null,
                'Niveau suspendu avec succès.',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur de suppression de niveau', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur de suppression de niveau : '.$th->getMessage());
        }
    }

    /**
     * Restaure un niveau suspendu.
     *
     * Un niveau peut être restauré si :
     * 1. Il n'y a pas de niveaux actifs avec un numéro supérieur au sien.
     * 2. Le niveau actif juste avant lui (numero - 1) existe et est actif.
     * OU s'il est le niveau 1 et qu'aucun niveau n'est actif.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        // Seuls les administrateurs (type_id > 1) peuvent restaurer des niveaux
        if (auth()->user()->type_id < 2) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        $niveau = Niveau::onlyTrashed()->find($id); // Cherche seulement les niveaux soft-deleted

        if (!$niveau) {
            return response()->json(['message' => 'Niveau non trouvé ou non suspendu.'], 404);
        }

        // Vérifier la cohérence pour la restauration
        $highestActiveNumero = Niveau::max('numero'); // Niveau actif le plus élevé

        if ($niveau->numero > ($highestActiveNumero + 1)) {
            return response()->json(['message' => 'Impossible de restaurer ce niveau. Vous devez restaurer les niveaux inférieurs en premier (le niveau actif le plus élevé est ' . ($highestActiveNumero ?? 'aucun') . ').'], 409);
        }

        // Si le niveau à restaurer n'est pas le niveau 1, vérifier que le niveau précédent est actif
        if ($niveau->numero > 1) {
            $previousNiveau = Niveau::find($niveau->numero - 1); // Cherche le niveau précédent actif
            if (!$previousNiveau || $previousNiveau->trashed()) {
                 return response()->json(['message' => 'Impossible de restaurer ce niveau. Le niveau précédent (Niveau ' . ($niveau->numero - 1) . ') doit exister et être actif.', 'niveau_manquant' => $niveau->numero - 1], 409);
            }
        }
        // Si c'est le niveau 1 et qu'il n'y a pas d'autres niveaux, c'est bon.

        $niveau->restore(); // Restaure le niveau

        return response()->json(['message' => 'Niveau restauré avec succès.', 'niveau' => $niveau], 200);
    }
}
