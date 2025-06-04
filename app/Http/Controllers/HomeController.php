<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;


class HomeController extends Controller
{
    protected QuizService $quizService;
    protected QuestionService $questionService;

    public function __construct(QuizService $quizService, QuestionService $questionService)
    {
        $this->middleware('auth');
        $this->quizService = $quizService;
        $this->questionService = $questionService;
    }

    public function index()
    {
        $quizzes = $this->quizService->getAll();
        foreach ($quizzes as $quiz) {
            $quiz->questions_count = $this->questionService->getTotalQuestionByQuizId($quiz->id);
        }
        return view('home', compact('quizzes'));
    }
}
