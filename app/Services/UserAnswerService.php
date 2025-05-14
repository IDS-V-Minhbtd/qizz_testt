<?php

namespace App\Services;

use App\Repositories\Interfaces\{
    UserAnswerRepositoryInterface,
    AnswerRepositoryInterface,
    QuestionRepositoryInterface,
    QuizRepositoryInterface
};
use Illuminate\Support\Facades\Auth;

class UserAnswerService
{
    public function __construct(
        protected UserAnswerRepositoryInterface $userAnswerRepo,
        protected AnswerRepositoryInterface $answerRepo,
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
    ) {}

   public function submitQuiz(int $quizId, array $answers, int $userId): array
{
    // Lấy quiz
    $quiz = $this->quizRepo->findById($quizId);
    if (!$quiz || !$quiz->is_public) {
        return [
            'success' => false,
            'message' => 'Quiz không khả dụng.'
        ];
    }

    // Tạo kết quả (result)
    $result = $this->resultService->createResult($quizId, $userId);

    // Lưu từng câu trả lời
    foreach ($answers as $questionId => $answerId) {
        if (is_array($answerId)) {
            $answerId = $answerId[0] ?? null;
        }

        if (!is_numeric($answerId)) {
            continue;
        }

        $this->saveAnswer([
            'quiz_id'     => $quizId,
            'question_id' => (int) $questionId,
            'user_id'     => $userId,
            'answer_id'   => (int) $answerId,
            'is_correct'  => $this->isCorrect($questionId, $answerId),
            'result_id'   => $result->id,
        ]);
    }

    return [
        'success' => true,
        'message' => 'Nộp bài thành công.',
        'result_id' => $result->id
    ];
}


    public function getQuizWithQuestions(int $quizId)
    {
        return $this->quizRepo->findByIdWithQuestions($quizId);
    }

    public function deleteAnswers(int $quizId, int $userId): void
    {
        $this->userAnswerRepo->deleteAnswers($quizId, $userId);
    }

    public function saveAnswer(array $data): void
    {
        $this->userAnswerRepo->create($data);
    }

    public function isCorrect(int $questionId, int $answerId): bool
    {
        $answer = $this->answerRepo->findById($answerId);
        return $answer && $answer->question_id === $questionId && $answer->is_correct;
    }

    public function getAnswersByQuiz(int $quizId, int $userId)
    {
        return $this->userAnswerRepo->getAllAnswersByQuiz($quizId, $userId);
    }

    public function getCorrectCount(int $quizId, int $userId): int
    {
        $answers = $this->userAnswerRepo->getAllAnswersByQuiz($quizId, $userId);

        return $answers->filter(function ($answer) {
            return $answer->is_correct;
        })->count();
    }
}
