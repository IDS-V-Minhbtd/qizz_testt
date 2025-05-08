<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Services\QuestionService;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function create($quizId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        $questions = $this->questionService->getByQuizId($quizId);

        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        return view('admin.quizzes.question.create', compact('quiz', 'questions'));
    }