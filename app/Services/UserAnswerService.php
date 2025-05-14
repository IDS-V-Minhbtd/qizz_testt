<?php

namespace App\Services;

use App\Repositories\Interfaces\{
    UserAnswerRepositoryInterface,
    AnswerRepositoryInterface,
    QuestionRepositoryInterface,
    QuizRepositoryInterface,
    ResultRepositoryInterface
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAnswerService
{
    public function __construct(
        protected UserAnswerRepositoryInterface $userAnswerRepo,
        protected AnswerRepositoryInterface $answerRepo,
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected ResultRepositoryInterface $resultRepo
    ) {}

    public function submitQuiz(int $quizId, array $answers, int $userId): array
    {
        // Kiểm tra quiz tồn tại và công khai
        $quiz = $this->quizRepo->findById($quizId);
        if (!$quiz || !$quiz->is_public) {
            Log::warning('Quiz not available', ['quiz_id' => $quizId]);
            return [
                'success' => false,
                'message' => 'Quiz không khả dụng.'
            ];
        }

        // Tạo bản ghi kết quả
        $result = $this->resultRepo->create([
            'user_id'      => $userId,
            'quiz_id'      => $quizId,
            'score'        => 0,
            'time_taken'   => 0,
            'completed_at' => now(),
        ]);

        // Lấy tất cả answer_id từ mảng answers để tối ưu truy vấn
        $answerIds = array_filter(array_map(function ($answerId) {
            return is_array($answerId) ? ($answerId[0] ?? null) : $answerId;
        }, $answers), function ($answerId) {
            return $answerId && is_numeric($answerId);
        });

        // Truy vấn tất cả answers trong một lần
        $answersData = $this->answerRepo->findByIds($answerIds)->keyBy('id');

        // Lặp qua mảng answers để lưu từng đáp án
        foreach ($answers as $questionId => $answerId) {
            // Xử lý trường hợp answerId là mảng
            if (is_array($answerId)) {
                $answerId = $answerId[0] ?? null;
            }

            // Kiểm tra answerId hợp lệ
            if (!$answerId || !is_numeric($answerId)) {
                Log::warning('Invalid answer_id', [
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'quiz_id' => $quizId
                ]);
                continue;
            }

            // Lấy answer từ danh sách đã truy vấn
            $answer = $answersData[$answerId] ?? null;
            if (!$answer || $answer->question_id !== (int) $questionId) {
                Log::warning('Answer not found or does not belong to question', [
                    'question_id' => $questionId,
                    'answer_id' => $answerId,
                    'quiz_id' => $quizId
                ]);
                continue;
            }

            // Chuẩn bị dữ liệu để lưu
            $data = [
                'result_id'   => $result->id,
                'question_id' => (int) $questionId,
                'answer_id'   => (int) $answerId, // Ép kiểu để đảm bảo là số
                'is_correct'  => $answer->is_correct,
            ];

            // Log dữ liệu trước khi tạo để gỡ lỗi
            Log::info('Creating user answer', $data);

            // Lưu vào bảng user_answers
            try {
                $this->userAnswerRepo->create($data);
            } catch (\Exception $e) {
                Log::error('Failed to create user answer', [
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // Trả về kết quả
        return [
            'success'   => true,
            'message'   => 'Nộp bài thành công.',
            'result_id' => $result->id
        ];
    }
}