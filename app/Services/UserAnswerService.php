<?php

namespace App\Services;

use App\Models\UserAnswer;
use App\Models\Result;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserAnswerServiceInterface;
use Illuminate\Support\Facades\DB;

class UserAnswerService implements UserAnswerServiceInterface
{
    public function __construct(
        protected UserAnswerRepositoryInterface $userAnswerRepo,
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected ResultRepositoryInterface $resultRepo,
        protected UserRepositoryInterface $userRepo
    ) {}

    // Lưu câu trả lời của người dùng
    public function create(array $data): UserAnswer
    {
        return $this->userAnswerRepo->create($data);
    }

    // Lấy câu trả lời theo ID
    public function getById(int $id): ?UserAnswer
    {
        return $this->userAnswerRepo->findById($id);
    }

    // Kiểm tra đáp án và lưu kết quả
    public function checkAnswer(int $userId, int $quizId, array $userAnswers): Result
    {
        return DB::transaction(function () use ($userId, $quizId, $userAnswers) {
            // Kiểm tra xem quiz có tồn tại không
            $quiz = $this->quizRepo->findById($quizId);
            if (!$quiz || $quiz->status !== 'public') {
                throw new \Exception('Quiz không hợp lệ hoặc chưa công khai.');
            }

            $totalQuestions = count($userAnswers);
            $correctAnswers = 0;

            // Lưu câu trả lời của người dùng
            foreach ($userAnswers as $questionId => $userAnswerData) {
                $question = $this->questionRepo->findById($questionId);
                if (!$question) {
                    continue;
                }

                // Lưu câu trả lời
                $userAnswer = $this->userAnswerRepo->create([
                    'user_id' => $userId,
                    'quiz_id' => $quizId,
                    'question_id' => $questionId,
                    'answer' => $userAnswerData['answer'],
                    'is_correct' => $this->checkIfAnswerCorrect($question, $userAnswerData['answer']),
                ]);

                // Kiểm tra đáp án đúng
                if ($userAnswer->is_correct) {
                    $correctAnswers++;
                }
            }

            // Tính điểm (tỷ lệ đúng)
            $score = ($correctAnswers / $totalQuestions) * 100;

            // Lưu kết quả vào bảng results
            $result = $this->resultRepo->create([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
            ]);

            return $result;
        });
    }

    // Kiểm tra đáp án đúng của câu hỏi (tùy thuộc vào loại câu hỏi)
    protected function checkIfAnswerCorrect($question, $answer): bool
    {
        // Xử lý cho các câu hỏi loại multiple_choice
        if ($question->type === 'multiple_choice') {
            $correctAnswer = $question->answers()->where('is_correct', true)->first();
            return $correctAnswer && $correctAnswer->answer === $answer;
        }

        // Xử lý cho câu hỏi loại true_false
        if ($question->type === 'true_false') {
            return ($answer === 'Đúng' && $question->correct_answer) || ($answer === 'Sai' && !$question->correct_answer);
        }

        // Xử lý cho câu hỏi loại text_input
        if ($question->type === 'text_input') {
            return strtolower(trim($answer)) === strtolower(trim($question->correct_answer));
        }

        return false;
    }

    // Lấy kết quả của người dùng theo quiz và user
    public function getResultByQuizAndUser(int $quizId, int $userId): ?Result
    {
        return $this->resultRepo->getByQuizAndUser($quizId, $userId);
    }

    // Lấy tất cả kết quả của người dùng
    public function getUserResults(int $userId)
    {
        return $this->resultRepo->getUserResults($userId);
    }
}
