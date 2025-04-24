<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Http\Requests\ValidateQuizRequest;
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
        // Fetch all quizzes and return the view
        $quizzes = $this->service->getAll();
        return view('quizzes.index', compact('quizzes'));
    }
    public function create()
    {
        return view('quizzes.create');
    }
    public function store(ValidateQuizRequest $request)
    {
        // Validate and create a new quiz
        $validatedData = $request->validated();

        $this->service->create($validatedData);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully!');
    }
    public function show($id)
    {
        $quiz = $this->service->getById((int) $id); // Cast $id to an integer
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        return view('quizzes.show', compact('quiz')); // Return the view with the quiz data
    }
    public function edit($id)
    {
        // Fetch the quiz by ID and return the edit view
        $quiz = $this->service->getById($id);
        return view('quizzes.edit', compact('quiz'));
    }
    public function update(ValidateQuizRequest $request, $id)
    {
        // Validate and update the quiz
        $validatedData = $request->validated();

        $this->service->update($id, $validatedData);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated successfully!');
    }
    public function destroy($id)
    {
        // Delete the quiz by ID
        $this->service->delete($id);

        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully!');
    }
}
