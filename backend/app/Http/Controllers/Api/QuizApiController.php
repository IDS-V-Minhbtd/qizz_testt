<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\QuizRequest;
use App\Services\QuizService;
use App\Models\Answer;

class QuizApiController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index()
    {
        $quizzes = $this->quizService->getAll();
        return response()->json([
            'status' => 'success',
            'data' => $quizzes
        ]);
    }

    public function show($id)
    {
        $quiz = $this->quizService->getById($id);
        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $quiz
        ]);
    }
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
        ]);

        $quiz = $this->quizService->create($validatedData);
        return response()->json([
            'status' => 'success',
            'message' => 'Quiz created successfully!',
            'data' => $quiz
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
        ]);

        $updated = $this->quizService->update($id, $validatedData);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Quiz updated successfully!'
        ]);
    }
    public function destroy($id)
    {
        $deleted = $this->quizService->delete($id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Quiz deleted successfully!'
        ]);
    }
}
