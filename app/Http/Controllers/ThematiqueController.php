<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Thematique;
use App\Models\Badge;
use App\Http\Controllers\PackageControlleur;
use Illuminate\Http\Request;
use App\Http\Requests\StoreThematiqueRequest;
use App\Http\Requests\UpdateThematiqueRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Niveau;

class ThematiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index($id = null)
    {
        try {

            if (isset($id)) {
                $thematiques = Thematique::find($id);
            }else{
                $thematiques = Thematique::all();
            }

            return PackageControlleur::successResponse(
                $thematiques,
                'Liste des thématiques récupérée avec succès',
                ['count' => $thematiques->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération thématiques', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des thématiques');
        }
    }

    public function index_playable()
    {
        try {
            // les thématiques avec au moins 3 questions leurs appartenant
            $thematiques = Thematique::with('niveau:id,numero')->whereNull('parent_id')
                ->whereHas('subThemes', function ($query) {
                    // On ne garde que les sous-thématiques qui ont au moins 3 questions
                    $query->has('questions', '>=', 3);
                })
                ->get();
            
            return PackageControlleur::successResponse(
                $thematiques,
                'Liste des thématiques récupérée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération thématiques : '.$th->getMessage(), ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des thématiques');
        }
    }
    
    public function get_one($id)
    {
        try {
            $thematique = Thematique::find($id);
            return PackageControlleur::successResponse(
                $thematique,
                'Thématique récupérée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération thématique', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération de la thématique');
        }
    }

    public function get_main_theme()
    {
        try {
            $thematiques = Thematique::with('niveau:id,numero')
                ->where('parent_id', null)
                ->get();
            return PackageControlleur::successResponse(
                $thematiques,
                'Liste des thématiques récupérée avec succès',
                ['count' => $thematiques->count()]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur récupération thématiques', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la récupération des thématiques');
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|integer|exists:thematiques,id', // parent_id doit exister dans la table thematiques
                'media_type' => 'string|max:15',
                'media_url' => 'nullable|url',
                'media_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,ogg,mp3,wav|max:20480', // Max 20MB
                'media_description' => 'nullable|string|max:255',
                'niveau_id' => 'nullable|integer|min:1|exists:niveaux,id',
                'nbr_min_point' => 'nullable|integer|min:0',
                'couleur' => 'nullable|string|max:10',
                'emoji' => 'nullable|string|max:10',
            ]);

            if ($validator->fails()) {
                \Log::error('Erreurs de validation', ['error' => $validator->errors()]);
                return PackageControlleur::errorResponse('Erreurs de validation'.$validator->errors(), 422);
            }

            $data = $request->only(['name', 'description', 'parent_id', 'media_type', 'media_description', 'niveau_id', 'nbr_min_point', 'couleur', 'emoji']);
            $data['media_url'] = null; // Initialiser à null

            // Gérer l'upload de média
            if ($request->hasFile('media_file')) {
                $file = $request->file('media_file');
                $path = $file->store('thematique', 'public'); // Stocke dans storage/app/public/thematique
                $data['media_url'] = Storage::url($path); // Obtient l'URL publique
            } elseif ($request->filled('media_url')) {
                $data['media_url'] = $request->input('media_url');
            }

            if(isset($data['parent_id']) && $data['parent_id'] != null){
                $themePrincipale = Thematique::find($data['parent_id']);
                if($themePrincipale){
                    $data['niveau_id'] = $themePrincipale->niveau_id;
                }
            }

            $thematique = Thematique::create($data);

            return PackageControlleur::successResponse(
                $thematique,
                'Thématique crée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur création thématique', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la création de la thématique'.$th->getMessage());
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $thematique = Thematique::find($id);
            if (!$thematique) {
                \Log::error('Thématique non trouvée.');
                return PackageControlleur::errorResponse('Thématique non trouvée.', 404, []);
            }

            $niveau = Niveau::find($thematique->niveau_id);
            $questions = [];
            $sousThematiques = [];
            if ($thematique->parent_id == null) {
                // Récupérer les sous-thématiques (s'il s'agit d'une thématique principale)
                $sousThematiques = Thematique::where('parent_id', $id)->get();
            }
            if ($thematique->parent_id != null) {
                // Récupérer les questions liées à cette thématique (s'il s'agit d'une sous thématique)
                $questions = Question::where('thematique_id', $id)->get();
            }

            //regroupement de données
            $data = [
                'thematique' => $thematique,
                'sousThematiques' => $sousThematiques,
                'questions' => $questions,
                'niveau' => $niveau
            ];

            return PackageControlleur::successResponse(
                $data,
                'Données Thématiques récupérées avec succès',
                ['count' => count($data)]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur reccupération données thématique ', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la reccupération des données thématique '.$th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $thematique = Thematique::find($id);
            if (!$thematique) {
                \Log::error('Thématique non trouvée.');
                return PackageControlleur::errorResponse('Thématique non trouvée.', 404, []);
            }
            return PackageControlleur::successResponse(
                $thematique,
                'Données Thématique récupérées avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur reccupération données thématique ', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la reccupération des données thématique '.$th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $thematique = Thematique::find($id);
            if (!$thematique) {
                return PackageControlleur::errorResponse('Thématique non trouvée.', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:thematiques,id',
                'media_type' => 'required|in:text,url,file',
                // Si c'est une URL, on valide le format
                'media_url' => 'required_if:media_type,url|nullable|url',
                'media_file' => 'nullable|file|max:20480',
                'media_description' => 'nullable|string|max:255',
                'niveau_id' => 'nullable|integer',
                'nbr_min_point' => 'nullable|integer|min:0',
                'couleur' => 'nullable|string|max:10',
                'emoji' => 'nullable|string|max:10',
            ]);
            
            if ($validator->fails()) {
                \Log::error('Détails erreur file:', [
                    'is_file' => $request->hasFile('media_file'),
                    'is_valid' => $request->file('media_file') ? $request->file('media_file')->isValid() : 'no file',
                    'error_code' => $request->file('media_file') ? $request->file('media_file')->getError() : 'N/A'
                ]);
                return PackageControlleur::errorResponse('Erreur validation : '.$validator->errors(), 422, $validator->errors());
            }

            // On récupère les données sauf les médias pour l'instant
            $data = $request->except(['media_file', 'media_url']);

            // FONCTION UTILITAIRE : Supprimer l'ancien fichier si nécessaire
            $deleteOldFile = function() use ($thematique) {
                if ($thematique->media_url && Str::contains($thematique->media_url, '/storage/thematique/')) {
                    $path = str_replace(Storage::url(''), '', $thematique->media_url);
                    Storage::disk('public')->delete($path);
                }
            };

            // GESTION DU MÉDIA SELON LE TYPE
            switch ($request->media_type) {
                case 'file':
                    if ($request->hasFile('media_file')) {
                        $deleteOldFile(); // Supprime l'ancien
                        $path = $request->file('media_file')->store('thematique', 'public');
                        $data['media_url'] = Storage::url($path);
                    } else {
                        // Si pas de nouveau fichier envoyé, on garde l'ancienne URL
                        $data['media_url'] = $thematique->media_url;
                    }
                    break;

                case 'url':
                    $deleteOldFile(); // On supprime l'ancien fichier local car on passe sur une URL
                    $data['media_url'] = $request->input('media_url');
                    break;

                case 'text':
                default:
                    $deleteOldFile();
                    $data['media_url'] = null;
                    $data['media_description'] = null;
                    break;
            }

            // Logique parent_id / niveau_id
            if ($request->filled('parent_id')) {
                $parent = Thematique::find($request->parent_id);
                if ($parent) {
                    $data['niveau_id'] = $parent->niveau_id;
                }
            }

            $thematique->update($data);

            return PackageControlleur::successResponse($thematique, 'Mise à jour réussie');

        } catch (\Throwable $th) {
            return PackageControlleur::errorResponse('Erreur : ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $thematique = Thematique::find($id);
            if (!$thematique) {
                \Log::error('Thématique non trouvée.');
                return PackageControlleur::errorResponse('Thématique non trouvée.', 404, []);
            }

            // Supprimer le fichier média associé si c'est un fichier local
            if ($thematique->media_url && Str::startsWith($thematique->media_url, '/storage/thematique/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $thematique->media_url));
            }

            // Si c'est une thématique principale, supprimer récursivement ses sous-thématiques
            if (is_null($thematique->parent_id)) {
                Thematique::where('parent_id', $thematique->id)->delete(); // Soft delete des sous-thèmes
            }

            Question::where('thematique_id', $thematique->id)->delete();
            Badge::where('thematique_id', $thematique->id)->delete();

            $thematique->delete(); // Soft delete de la thématique principale

            return PackageControlleur::successResponse(
                [],
                'Thématique supprimée avec succès.',
                ['count' => 0]
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur lors de la suppression de la thématique.', ['error' => $th->getMessage()]);
            return PackageControlleur::errorResponse('Erreur lors de la suppression de la thématique'.$th->getMessage());
        }
    }

}
