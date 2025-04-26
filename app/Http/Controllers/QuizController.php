<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Http\Requests\QuizRequest;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    protected $service;

    public function __construct(QuizService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        // Lấy danh sách tất cả các quiz và trả về view
        $quizzes = $this->service->getAll();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        // Pass an empty Quiz instance to the view if needed
        $quiz = new Quiz();
        return view('admin.quizzes.create', compact('quiz'));
    }

    public function store(QuizRequest $request)
    {
        // Xử lý và tạo quiz mới
        $validatedData = $request->validated();

        // Tạo quiz thông qua service
        $this->service->create($validatedData);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully!');
    }

    public function edit($id)
    {
        
        $quiz = $this->service->getById($id);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }
        $questions = $quiz->questions()->orderBy('order')->get();
        

        return view('admin.quizzes.edit', compact('quiz', 'questions'));
    }

    public function update(QuizRequest $request, $id)
    {
        // Process and update the quiz
        $validatedData = $request->validated();

        // Update quiz through the service
        $this->service->update($id, $validatedData);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated successfully!');
    }

    public function destroy($id)
    {
        // Delete the quiz by ID
        $this->service->delete($id);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }
}
