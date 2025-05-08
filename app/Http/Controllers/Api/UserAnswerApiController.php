<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserAnswerService;
use App\Services\ResultService;
use App\Models\Answer;

class UserAnswerApiController extends Controller
{
    protected $userAnswerService;
    protected $resultService;

    public function __construct(UserAnswerService $userAnswerService, ResultService $resultService)
    {
        $this->userAnswerService = $userAnswerService;
        $this->resultService = $resultService;
    }

    // Kiểm tra câu trả lời đúng/sai
    public function checkAnswer(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'required|exists:answers,id',
        ]);

        $questionId = $validated['question_id'];
        $answerId = $validated['answer_id'];

        // Fetch the correct answer for the question
        $correctAnswer = $this->answerRepo->getByQuestionId($questionId)
            ->firstWhere('is_correct', true);

        if (!$correctAnswer) {
            return response()->json([
                'success' => false,
                'message' => 'No correct answer found for this question.',
            ], 400);
        }

        // Check if the provided answer is correct
        $isCorrect = $correctAnswer->id === $answerId;

        return response()->json([
            'success' => true,
            'correct' => $isCorrect,
            'message' => $isCorrect ? 'Correct answer!' : 'Wrong answer!',
        ]);
    }

    // Nộp toàn bộ kết quả quiz
    public function submit(Request $request)
{
    $data = $request->validate([
        'quiz_id' => 'required|integer|exists:quizzes,id',
        'score' => 'required|integer',
        'time_taken' => 'required|integer',
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|integer|exists:questions,id',
        'answers.*.answer_id' => 'required|integer|exists:answers,id',
        'answers.*.is_correct' => 'required|boolean',
    ]);

    $userId = Auth::id();

    // ✅ Tạo result trước
    $result = $this->resultService->createResult($data);

    // ✅ Lưu từng câu trả lời
    foreach ($data['answers'] as $answer) {
        $this->userAnswerService->saveAnswer([
            'quiz_id' => $data['quiz_id'],
            'question_id' => $answer['question_id'],
            'answer_id' => $answer['answer_id'],
            'user_id' => $userId,
            'is_correct' => $answer['is_correct'],
            'result_id' => $result->id,
        ]);
    }

    return response()->json(['result_id' => $result->id], 201);
}

}
