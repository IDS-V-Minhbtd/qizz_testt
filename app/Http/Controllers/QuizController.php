<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Http\Requests\QuizRequest;
use App\Models\Quiz;

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
        $quizzes = $this->quizService->getAll();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $quiz = new Quiz();
        return view('admin.quizzes.create', compact('quiz'));
    }

    public function store(QuizRequest $request)
    {
        $validatedData = $request->validated();
        $this->quizService->create($validatedData);

        return redirect()->route('admin.quizzes.index')
                         ->with('success', 'Quiz created successfully!');
    }

    public function edit($id)
    {
        $quiz = $this->quizService->getById($id);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        // ✅ Lấy danh sách câu hỏi theo quiz_id với phân trang
        $questions = $this->questionService->paginateByQuizId($id, 10);

        return view('admin.quizzes.edit', compact('quiz', 'questions'));
    }

    public function update(QuizRequest $request, $id)
    {
        $validatedData = $request->validated();
        $this->quizService->update($id, $validatedData);

        return redirect()->route('admin.quizzes.index')
                         ->with('success', 'Quiz updated successfully!');
    }

    public function destroy($id)
    {
        $this->quizService->delete($id);

        return redirect()->route('admin.quizzes.index')
                         ->with('success', 'Quiz deleted successfully!');
    }
}
