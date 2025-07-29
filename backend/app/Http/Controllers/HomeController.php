<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Models\Catalog;


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
        $catalogs = Catalog::with(['quizzes' => function($query) {
            $query->where('is_public', true);
        }])->get();

        // Đảm bảo $quiz->questions_count luôn là số, tránh lỗi count() với null
        foreach ($quizzes as $quiz) {
            $quiz->questions_count = (int) $this->questionService->getTotalQuestionByQuizId($quiz->id);
        }

        return view('home', compact('quizzes', 'catalogs'));
    }
    public function search(Request $request)
    {
        $keyword = $request->input('search');
        $quizzes = $this->quizService->search($keyword);
        $data = $this->quizService->getAll();
        foreach ($quizzes as $quiz) {
            $quiz->questions_count = $this->questionService->getTotalQuestionByQuizId($quiz->id);
        
        }
        return view('home', compact('quizzes'));
    }
}
