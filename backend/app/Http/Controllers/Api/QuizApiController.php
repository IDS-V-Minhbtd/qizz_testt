<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuizRequest;
use App\Services\QuizService;
use App\Models\Answer;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index()
    {
        $quizzes = $this->quizService->getAllQuizzes();
        return response()->json($quizzes);
    }

    public function show($id)
    {
        $quiz = $this->quizService->getQuizById($id);
        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }
        return response()->json($quiz);
    }
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
        ]);

        $quiz = $this->quizService->createQuiz($validatedData);
        return response()->json($quiz, 201);
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
        ]);

        $quiz = $this->quizService->updateQuiz($id, $validatedData);
        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }
        return response()->json($quiz);
    }
    public function destroy($id)
    {
        $deleted = $this->quizService->deleteQuiz($id);
        if (!$deleted) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }
        return response()->json(['message' => 'Quiz deleted successfully']);
    }
}
