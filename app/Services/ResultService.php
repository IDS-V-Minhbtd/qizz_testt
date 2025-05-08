<?php
namespace App\Services;

use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ResultService
{
    public function __construct(
        protected ResultRepositoryInterface $resultRepo,
        protected UserAnswerRepositoryInterface $userAnswerRepo
    ) {}

    public function createResult(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Tạo result mới
            $result = $this->resultRepo->create([
                'user_id'     => Auth::id(),
                'quiz_id'     => $data['quiz_id'],
                'score'       => $data['score'],
                'time_taken'  => $data['time_taken'],
                'completed_at'=> now(),
            ]);

            // Lưu tất cả câu trả lời của user
            foreach ($data['user_answers'] as $answer) {
                $this->userAnswerRepo->create([
                    'result_id'   => $result->id,
                    'question_id' => $answer['question_id'],
                    'answer_id'   => $answer['answer_id'], // Đảm bảo sử dụng 'answer_id'
                    'is_correct'  => $answer['is_correct'],
                ]);
            }

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
}
