<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Http\Requests\QuizRequest;
use App\Models\Quiz;
use App\Models\Catalog;

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
        // Sửa lỗi: khai báo biến $quizzes đúng
        if ($user->role === 'quizz_manager') {
            $quizzes = $this->quizService->getQuizzesForManager($user->id);
        } else {
            $quizzes = $this->quizService->getAll();
        }
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $quiz = new Quiz();
        $catalogs = Catalog::all();
        return view('admin.quizzes.create', compact('quiz', 'catalogs'));
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
        $user = auth()->user();

        if ($user->role === 'quizz_manager' && $quiz->created_by !== $user->id) {
            return redirect()->route('admin.quizzes.index')
                             ->with('error', 'You do not have permission to edit this quiz.');
        }

        $questions = $this->questionService->paginateByQuizId($id, 10);
        $catalogs = Catalog::all();

        return view('admin.quizzes.edit', compact('quiz', 'questions', 'catalogs'));
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
   public function import(Quiz $quiz)
{
    return view('admin.questions.import', compact('quiz'));
}

public function importUpload(Quiz $quiz)
{
    return view('admin.questions.import_upload', compact('quiz'));
}

public function importTemplate(Quiz $quiz)
{
    return view('admin.questions.import_template', compact('quiz'));
}


}
