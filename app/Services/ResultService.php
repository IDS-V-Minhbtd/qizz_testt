<?php
namespace App\Services;

use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

    public function submitQuizAndSaveResult(int $quizId, array $answers, int $userId, int $timeTaken)
{
    return DB::transaction(function () use ($quizId, $answers, $userId, $timeTaken) {
        $quiz = $this->quizRepo->findById($quizId);
        if (!$quiz || !$quiz->is_public) {
            return [
                'success' => false,
                'message' => 'Quiz không khả dụng.'
            ];
        }

        $user = \App\Models\User::find($userId);

        // Cấp quyền quizz_manager miễn phí nếu đủ điều kiện
        if ($user->results()->count() >= 5 && !$user->quizz_manager_until) {
            $user->update([
                'role' => 'quizz_manager',
                'quizz_manager_until' => Carbon::now()->addDays(7),
            ]);
            session()->flash('success', 'Bạn đã được cấp quyền Quizz Manager miễn phí trong 7 ngày!');
        }

        $score = 0;

        $result = $this->resultRepo->create([
            'user_id'     => $userId,
            'quiz_id'     => $quizId,
            'score'       => 0, // sẽ update sau
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
                $score++;
            }

            $this->userAnswerRepo->create([
                'result_id'   => $result->id,
                'question_id' => (int) $questionId,
                'answer_id'   => (int) $answerId,
                'is_correct'  => $isCorrect,
            ]);
        }

        // Update điểm sau khi tính xong
        $this->resultRepo->update($result->id, [
            'score' => $score,
            'time_taken' => $timeTaken,
        ]);

        return $result;
    });
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
      public function getResultsByUserId($userId)
    {
        return $this->resultRepo->getByUserId($userId);
    }
}
