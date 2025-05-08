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

    public function submitAnswer(int $quizId, int $questionId, int $answerId): array
    {
        $userId = Auth::id();

        // Kiểm tra câu hỏi thuộc quiz
        $question = $this->questionRepo->findByQuizIdAndQuestionId($quizId, $questionId);
        if (!$question) {
            return ['success' => false, 'message' => 'Câu hỏi không hợp lệ.'];
        }

        // Kiểm tra đáp án đúng
        $answer = $this->answerRepo->findById($answerId);
        if (!$answer || $answer->question_id !== $questionId) {
            return ['success' => false, 'message' => 'Đáp án không hợp lệ.'];
        }

        $isCorrect = (bool) $answer->is_correct;

        // Lưu user answer
        $this->userAnswerRepo->create([
            'quiz_id'     => $quizId,
            'question_id' => $questionId,
            'answer_id'   => $answerId,
            'user_id'     => $userId,
            'is_correct'  => $isCorrect,
            'result_id'   => $resultId,
        ]);

        return [
            'success' => true,
            'correct' => $isCorrect,
            'message' => $isCorrect ? 'Chính xác!' : 'Sai rồi!',
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
        return $this->userAnswerRepo->getAllAnswersByQuiz($quizId, $userId); // Gọi phương thức repo
    }
    // Trong UserAnswerService
public function getCorrectCount(int $quizId, int $userId): int
{
    // Giả sử bạn đã có phương thức getAnswersByQuiz trả về các câu trả lời của người dùng
    $answers = $this->userAnswerRepo->getAllAnswersByQuiz($quizId, $userId);

    // Lọc ra các câu trả lời đúng
    return $answers->filter(function ($answer) {
        return $answer->is_correct;
    })->count();
}

}
