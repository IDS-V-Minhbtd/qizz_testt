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

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        // Validate and store the question
        $request->validated();
        $this->service->create($request->all());

        return redirect()->route('questions.index')->with('success', 'Question created successfully!');
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
