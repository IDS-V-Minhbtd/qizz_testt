<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestionService;

class QuestionController extends Controller
{
    protected $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        // Fetch all questions and return the view
        $questions = $this->service->getAll();
        return view('questions.index', compact('questions'));
    }

    public function create($quizId)
    {
        return view('questions.create', ['quizId' => $quizId]);
    }

    public function store(Request $request, $quizId)
    {
        // Validate and store the question
        $validatedData = $request->validate([
            'question' => 'required|string|max:500',
            'order' => 'nullable|integer|min:1',
            'answer_type' => 'required|string|in:multiple_choice,text_input',
        ]);

        $validatedData['quiz_id'] = $quizId; // Associate the question with the quiz
        $this->service->create($validatedData);

        return redirect()->route('quizzes.questions.index', $quizId)
            ->with('success', 'Question added successfully!');
    }

    public function edit($id)
    {
        // Fetch the question by ID and return the edit view
        $question = $this->service->getById($id);
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        // Validate and update the question
        $request->validated();
        $this->service->update($id, $request->all());

        return redirect()->route('questions.index')->with('success', 'Question updated successfully!');
    }

    public function destroy($id)
    {
        // Delete the question by ID
        $this->service->delete($id);

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully!');
    }
}
