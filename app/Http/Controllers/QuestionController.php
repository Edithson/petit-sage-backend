<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Services\QuestionService;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function giveQuestionNumber()
    {
        $listQuestions = Question::all();
        foreach ($listQuestions as $key => $question) {
            $question->numero = $key + 1;
            $question->save();
        }
        return response()->json([
            'message' => 'Numéros des questions mis à jour avec succès!',
            'data' => $listQuestions
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Question::select([
                'id', 
                'intitule_text', 
                'intitule_media_description', 
                'thematique_id', 
                'partie_id', 
                'degre_difficulte'
            ])
            ->with([
                'thematique:id,name',
                'partie:id,numero'
            ])->orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('intitule_text', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('intitule_media_description', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            if ($request->filled('theme_id') && $request->theme_id !== 'all') {
                $query->where('thematique_id', $request->theme_id);
            }

            $perPage = $request->get('per_page', 15);
            $questions = $query->paginate($perPage);

            return ResponseHelper::successResponse(
                $questions,
                'Liste des questions récupérée avec succès'
            );
        } catch (\Throwable $th) {
            Log::error('Erreur récupération questions', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la récupération des questions : ' . $th->getMessage());
        }
    }

    public function showQuestionThematics($id)
    {
        try {
            $questions = Question::where('thematique_id', $id)->orderBy('numero')->get();
            return ResponseHelper::successResponse(
                $questions,
                'Liste des questions récupérée avec succès',
                ['count' => count($questions)]
            );
        } catch (\Throwable $th) {
            Log::error('Erreur récupération questions', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la récupération des questions : ' . $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionRequest $request)
    {
        $user = $request->user();
        if (!$user) {
            Log::error('Token invalide');
            return ResponseHelper::errorResponse('Token invalide', 401);
        }

        try {
            $question = $this->questionService->createQuestion($request->validated(), $request);

            return ResponseHelper::successResponse(
                $question,
                'Question créée avec succès',
                ['count' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Erreur création question', ['error' => $e->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la création de la question : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // selectionné la question avec ses relations thematique, partie et creator
            $question = Question::with(['thematique', 'partie', 'creator'])->find($id);
            if (!$question) {
                return ResponseHelper::errorResponse('Question non trouvée.', 404, []);
            }
            return ResponseHelper::successResponse(
                $question,
                'Question récupérée avec succès',
                ['count' => 1]
            );
        } catch (\Throwable $th) {
            Log::error('Erreur sélection question', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la sélection de la question : ' . $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, $id)
    {
        $question = Question::find($id);
        if (!$question) {
            Log::error('Question non trouvée.');
            return ResponseHelper::errorResponse('Question non trouvée.', 404, []);
        }

        try {
            $updatedQuestion = $this->questionService->updateQuestion($question, $request->validated(), $request);

            return ResponseHelper::successResponse(
                $updatedQuestion,
                'Question modifiée avec succès!',
                ['count' => 1]
            );
        } catch (\Exception $e) {
            Log::error('Erreur modification question', ['error' => $e->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la modification de la question.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function reorder(Request $request)
    {
        try {
            $questionsData = $request->validate([
                '*.id' => 'required|integer|exists:questions,id',
                '*.numero' => 'required|integer|min:1',
            ]);
            
            DB::beginTransaction();
            
            foreach ($questionsData as $question) {
                Question::where('id', $question['id'])->update(['numero' => $question['numero']]);
            }

            DB::commit();

            return ResponseHelper::successResponse(
                null,
                'Numéros des questions mis à jour avec succès.',
                ['count' => count($questionsData)]
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Erreurs de validation lors de la réorganisation', ['errors' => $e->errors()]);
            return ResponseHelper::errorResponse('Erreurs de validation', 422, $e->errors());
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour des numéros de questions', ['error' => $th->getMessage()]);
            return ResponseHelper::errorResponse('Erreur serveur interne lors de la mise à jour.\n' . $th->getMessage(), 500, []);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            Log::error('Question non trouvée.');
            return ResponseHelper::errorResponse('Question non trouvée.', 404, []);
        }

        try {
            $this->questionService->deleteQuestion($question);

            $data = Question::where('thematique_id', $question->thematique_id)
                ->where('partie_id', $question->partie_id)
                ->orderBy('numero')
                ->get();

            return ResponseHelper::successResponse(
                $data,
                'Question supprimée et numéros réorganisés avec succès!',
                ['count' => 0]
            );

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la question.', ['error' => $e->getMessage()]);
            return ResponseHelper::errorResponse('Erreur lors de la suppression de la question : ' . $e->getMessage());
        }
    }
}
