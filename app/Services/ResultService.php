<?php
namespace App\Services;

use App\Repositories\Interfaces\ResultRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;

class ResultService
{
    public function __construct(
        protected ResultRepositoryInterface $resultRepo,
        protected QuizRepositoryInterface $quizRepo
    ) {}

    public function create(array $data)
    {
        return $this->resultRepo->create($data);
    }

    public function getById($id)
    {   
        return $this->resultRepo->find($id);
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
