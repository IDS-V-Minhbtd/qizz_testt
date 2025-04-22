<?php
namespace App\Services;

use App\Models\Quiz;
use App\Repositories\Interfaces\QuizRepositoryInterface;

class QuizService
{
    public function __construct(protected QuizRepositoryInterface $quizRepo) {}

    public function create(array $data): Quiz
    {
        return $this->quizRepo->create($data);
    }

    public function getById(int $id): ?Quiz
    {
        return $this->quizRepo->findById($id);
    }

    public function getAll(): iterable
    {
        return $this->quizRepo->all();
    }

    // public function calculateUserScore(array $questions, array $userAnswers): float
    // {
    //     $totalQuestions = count($questions);
    //     $correctAnswers = 0;

    //     foreach ($questions as $question) {
    //         $userAnswer = collect($userAnswers)->firstWhere('question_id', $question['id']);
    //         if ($userAnswer && $userAnswer['answer_id'] === $question['correct_answer_id']) {
    //             $correctAnswers++;
    //         }
    //     }

    //     return $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
    // }
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
