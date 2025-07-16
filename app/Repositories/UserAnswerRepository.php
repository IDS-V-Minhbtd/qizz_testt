<?php

namespace App\Repositories;

use App\Models\UserAnswer;
use App\Models\Result;
use App\Models\Answer;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;


class UserAnswerRepository implements UserAnswerRepositoryInterface
{
    protected UserAnswer $model;

    public function __construct(UserAnswer $model)
    {
        $this->model = $model;
    }

    public function create(array $data): UserAnswer
{
    $resultId = $data['result_id'];
    $questionId = $data['question_id'];
    $answerId = $data['answer_id'];
    $isCorrect = $data['is_correct'];

    return $this->model->create([
        'result_id' => $resultId,
        'question_id' => $questionId,
        'answer_id' => $answerId,
        'is_correct' => $isCorrect,
    ]);
}

    public function findById(int $id): ?UserAnswer
    {
        return $this->model->find($id);
    }

    public function isCorrect(int $id): bool
    {
        $userAnswer = $this->findById($id);
        return $userAnswer ? $userAnswer->is_correct : false;
    }

    public function getUserResults(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getUserAnswerByQuizAndQuestion(int $quizId, int $questionId, int $userId): ?UserAnswer
    {
        return $this->model->whereHas('result', function ($query) use ($quizId, $userId) {
                $query->where('quiz_id', $quizId)
                      ->where('user_id', $userId);
            })
            ->where('question_id', $questionId)
            ->first();
    }

    public function getAllAnswersByQuiz(int $quizId, int $userId): Collection
    {
        return $this->model->whereHas('result', function ($query) use ($quizId, $userId) {
                $query->where('quiz_id', $quizId)   
                      ->where('user_id', $userId);
            })
            ->get();
    }

    public function deleteAnswers(int $quizId, int $userId): void
    {
        $this->model->whereHas('result', function ($query) use ($quizId, $userId) {
                $query->where('quiz_id', $quizId)
                      ->where('user_id', $userId);
            })
            ->delete();
    }

    public function getResultByQuizAndUser(int $quizId, int $userId): ?UserAnswer
{
    return $this->model->whereHas('result', function ($query) use ($quizId, $userId) {
        $query->where('quiz_id', $quizId)
              ->where('user_id', $userId);
    })->first();
}

    public function getCorrectAnswersCount(int $quizId, int $userId): int
    {
        return $this->model->whereHas('result', function ($query) use ($quizId, $userId) {
                $query->where('quiz_id', $quizId)
                      ->where('user_id', $userId);
            })
            ->where('is_correct', true)
            ->count();
    }
    public function CheckCorrectAnswer(int $questionId, int $answerId): bool
    {
        $answer = Answer::find($answerId);
        if (!$answer) {
            return false;
        }

        // Kiểm tra xem câu trả lời có đúng với câu hỏi không
        return $answer->question_id === $questionId && $answer->is_correct;
    }
}
