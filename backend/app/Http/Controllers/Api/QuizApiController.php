<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Http\Requests\QuizRequest;
use App\Http\Controllers\Controller;

class QuizApiController extends Controller
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
        return response()->json($quizzes);
    }

    public function store(QuizRequest $request)
    {
        $validatedData = $request->validated();
        $quiz = $this->quizService->create($validatedData);

        return response()->json($quiz, 201);
    }

    public function show($id)
    {
        $quiz = $this->quizService->getById($id);
        return response()->json($quiz);
    }

    public function update(QuizRequest $request, $id)
    {
        $validatedData = $request->validated();
        $quiz = $this->quizService->update($id, $validatedData);

        return response()->json($quiz);
    }

    public function destroy($id)
    {
        $this->quizService->delete($id);

        return response()->json(['message' => 'Quiz deleted successfully!'], 204);
    }

    public function import(Request $request)
    {
        // Implement your import logic here to return JSON
        return response()->json(['message' => 'Quiz import initiated.', 'file' => $request->file('file')->getClientOriginalName() ?? 'No file provided']);
    }

    public function clone($id)
    {
        // Implement your clone logic here to return JSON
        return response()->json(['message' => 'Quiz cloned successfully!', 'cloned_quiz_id' => $id]);
    }
}
