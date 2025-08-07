<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Http\Requests\QuizRequest;
use App\Http\Resources\QuizResource;

class QuizController extends Controller
{
    protected QuizService $quizService;
    protected QuestionService $questionService;

    public function __construct(QuizService $quizService, QuestionService $questionService)
    {
        $this->quizService = $quizService;
        $this->questionService = $questionService;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'quizz_manager') {
            $quizzes = $this->quizService->getQuizzesForManager($user->id);
        } else {
            $quizzes = $this->quizService->getAll();
        }

        return QuizResource::collection($quizzes);
    }

    public function store(QuizRequest $request)
    {
        $quiz = $this->quizService->create($request->validated());

        return response()->json([
            'message' => 'Quiz created successfully!',
            'data' => new QuizResource($quiz),
        ], 201);
    }

    public function show($id)
    {
        $quiz = $this->quizService->getById($id);
        $catalog = $this->quizService->getCatalogs();
        $quiz->load(['catalog', 'questions']);

        return new QuizResource($quiz);
    }

    public function update(QuizRequest $request, $id)
    {
        $quiz = $this->quizService->update($id, $request->validated());

        return response()->json([
            'message' => 'Quiz updated successfully!',
            'data' => new QuizResource($quiz),
        ]);
    }

    public function destroy($id)
    {
        $this->quizService->delete($id);

        return response()->json([
            'message' => 'Quiz deleted successfully!',
        ]);
    }

    // Optional: Get import view info if needed via API
    public function importInfo($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return response()->json([
            'quiz' => new QuizResource($quiz),
        ]);
    }

    // Download template file (React có thể tải bằng link này)
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/question_import_template.xlsx'));
    }
}
