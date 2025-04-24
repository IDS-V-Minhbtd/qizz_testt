<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;

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
}
