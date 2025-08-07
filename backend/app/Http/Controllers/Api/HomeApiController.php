<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\QuestionService;
use App\Models\Catalog;
use App\Http\Resources\QuizResource;
use App\Http\Controllers\Controller;

class HomeApiController extends Controller
{
    protected $quizService;
    protected $questionService;

    public function __construct(QuizService $quizService, QuestionService $questionService)
    {
        $this->middleware('auth:sanctum');
        $this->quizService = $quizService;
        $this->questionService = $questionService;
    }

    /**
     * Lấy danh sách quizzes và catalogs
     */
    public function index()
    {
        $quizzes = $this->quizService->getAll();
        $catalogs = Catalog::with(['quizzes' => function ($query) {
            $query->where('is_public', true);
        }])->get();

        // Thêm questions_count cho mỗi quiz
        foreach ($quizzes as $quiz) {
            $quiz->questions_count = (int) $this->questionService->getTotalQuestionByQuizId($quiz->id);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'quizzes' => QuizResource::collection($quizzes),
                'catalogs' => $catalogs->map(function ($catalog) {
                    return [
                        'id' => $catalog->id,
                        'name' => $catalog->name,
                        'quizzes' => QuizResource::collection($catalog->quizzes),
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Tìm kiếm quizzes theo từ khóa
     */
    public function search(Request $request)
    {
        $keyword = $request->input('search');
        $quizzes = $this->quizService->search($keyword);

        foreach ($quizzes as $quiz) {
            $quiz->questions_count = (int) $this->questionService->getTotalQuestionByQuizId($quiz->id);
        }

        return response()->json([
            'success' => true,
            'data' => QuizResource::collection($quizzes)
        ], 200);
    }
}