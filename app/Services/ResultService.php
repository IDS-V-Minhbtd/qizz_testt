<?php
namespace App\Services;

use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResultService
{
    public function __construct(
        protected ResultRepositoryInterface $resultRepo,
        protected UserAnswerRepositoryInterface $userAnswerRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected AnswerRepositoryInterface $answerRepo,
    ) {}

    public function isCorrect(int $questionId, int $answerId): bool
{
    // Lấy câu trả lời từ cơ sở dữ liệu để kiểm tra
    $answer = $this->answerRepo->findById($answerId);

    // Kiểm tra nếu câu trả lời hợp lệ và câu trả lời đó có đúng hay không
    return $answer && $answer->question_id === $questionId && $answer->is_correct;
}

    public function submitQuizAndSaveResult(int $quizId, array $answers, int $userId)
{
    // Lấy quiz
    $quiz = $this->quizRepo->findById($quizId);
    if (!$quiz || !$quiz->is_public) {
        return [
            'success' => false,
            'message' => 'Quiz không khả dụng.'
        ];
    }

    // Tính điểm và thời gian làm bài
    $score = 0;
    $timeTaken = 0; // Bạn có thể tính toán thời gian tại đây nếu có.

    $userAnswers = []; // Mảng để lưu tất cả câu trả lời của người dùng.

    // Create a result entry for the user
    $result = $this->resultRepo->create([
        'user_id'     => $userId,
        'quiz_id'     => $quizId,
        'score'       => $score, // Sẽ cập nhật điểm sau khi kiểm tra câu trả lời.
        'time_taken'  => $timeTaken,
        'completed_at'=> now(),
    ]);

    foreach ($answers as $questionId => $answerId) {
        if (is_array($answerId)) {
            $answerId = $answerId[0] ?? null;
        }

        if (!is_numeric($answerId)) {
            continue;
        }

        $isCorrect = $this->isCorrect($questionId, $answerId);
        if ($isCorrect) {
            $score++; // Cộng điểm nếu đúng
        }

        $userAnswers[] = [
            'question_id' => $questionId,
            'answer_id'   => $answerId,
            'is_correct'  => $isCorrect,
        ];

        $this->userAnswerRepo->create([
            'result_id'   => $result->id,
            'question_id' => (int) $questionId,
            'answer_id'   => (int) $answerId,
            'is_correct'  => $isCorrect,
        ]);
    }

    // Cập nhật điểm và thời gian làm bài vào kết quả
    $this->resultRepo->update($result->id, [
        'score'      => $score,
        'time_taken' => $timeTaken,
    ]);

    return $result; 
}


    public function getResultWithAnswers($id)
    {
        return $this->resultRepo->findByIdWithAnswers($id); // Đảm bảo repo trả về kết quả với các câu trả lời liên quan
    }

    public function calculateUserScore(array $questions, array $userAnswers): array
    {
        $totalQuestions = count($questions);
        $correctAnswers = 0;

        $userAnswersCollection = collect($userAnswers);

        foreach ($questions as $question) {
            $userAnswer = $userAnswersCollection->firstWhere('question_id', $question['id']);
            if ($userAnswer && $userAnswer['answer_id'] === $question['correct_answer_id']) {
                $correctAnswers++;
            }
        }

        $wrongAnswers = $totalQuestions - $correctAnswers;
        $scorePercent = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0.0;

        return [
            'score' => $scorePercent,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers
        ];
    }
}
