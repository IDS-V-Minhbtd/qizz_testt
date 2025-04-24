<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Http\Requests\AnswerRequest;    
use App\Services\AnswerService;

class AnswerController extends Controller
{
    public function __construct(AnswerService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $answers = $this->service->getAll();
        return view('answers.index', compact('answers'));
    }
    public function create()
    {
        return view('answers.create');
    }
    public function store(AnswerRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('answers.index')->with('success', 'Answer created successfully!');
    }
    public function edit($id)
    {
        $answer = $this->service->getById($id);
        return view('answers.edit', compact('answer'));
    }
    
}
