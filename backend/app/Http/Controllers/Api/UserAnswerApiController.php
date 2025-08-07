<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserAnswerService;
use App\Services\QuizService;
use App\Services\ResultService;
use App\Http\Resources\ResultResource;
use App\Http\Resources\UserAnswerResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserAnswerApiController extends Controller
{
    protected $userAnswerService;
    protected $quizService;
    protected $resultService;

    public function __construct(
        UserAnswerService $userAnswerService,
        QuizService $quizService,
        ResultService $resultService
    ) {
        $this->userAnswerService = $userAnswerService;
        $this->quizService = $quizService;
        $this->resultService = $resultService;
    }

    /**
     * Lấy thông tin quiz để bắt đầu làm bài
     */
    public function start($quizId)
    {
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz || !$quiz->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz không khả dụng.'
            ], 404);
        }

        // Lấy câu hỏi và đáp án, xáo trộn câu hỏi
        $questions = $quiz->questions()->with(['answers' => function ($q) {
            $q->select('id', 'question_id', 'answer', 'is_correct');
        }])->orderBy('order')->get()->shuffle()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => [
                    'id' => $quiz->id,
                    'name' => $quiz->name,
                    'description' => $quiz->description,
                    'time_limit' => $quiz->time_limit,
                ],
                'questions' => $questions
            ]
        ], 200);
    }

    /**
     * Gửi bài làm và lưu kết quả
     */
    public function submit(Request $request, $quizId)
    {
        $userId = Auth::id();
        $answers = $request->input('answers');
        $timeTaken = $request->input('time_taken');

        if (!is_array($answers)) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.'
            ], 400);
        }

        try {
            $result = $this->resultService->submitQuizAndSaveResult($quizId, $answers, $userId, $timeTaken);
            $this->userAnswerService->updatePopularCountForAllQuizzes();

            return response()->json([
                'success' => true,
                'message' => 'Nộp bài thành công!',
                'data' => new ResultResource($result)
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Submit quiz error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy kết quả quiz theo result_id
     */
    public function result($resultId)
    {
        $result = $this->resultService->getResultById($resultId);

        if (!$result || $result->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết quả.'
            ], 404);
        }

        $result->load('quiz', 'userAnswers.question', 'userAnswers.answer');
        return new ResultResource($result);
    }

    /**
     * Lấy kết quả quiz theo quiz_id
     */
    public function resultByQuiz($quizId)
    {
        $userId = Auth::id();
        $result = $this->resultService->getResultWithAnswers($quizId, $userId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết quả.'
            ], 404);
        }

        $result->load('quiz', 'userAnswers.question', 'userAnswers.answer');
        return new ResultResource($result);
    }

    /**
     * Kiểm tra đáp án đúng/sai (AJAX)
     */
    public function checkAnswer(Request $request)
    {
        $questionId = $request->input('question_id');
        $answerId = $request->input('answer_id');

        if (!$questionId || !$answerId) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu thông tin câu hỏi hoặc đáp án.'
            ], 400);
        }

        try {
            $isCorrect = $this->userAnswerService->CheckCorrectAnswer($questionId, $answerId);
            return response()->json([
                'success' => true,
                'data' => [
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'is_correct' => $isCorrect
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Check answer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}