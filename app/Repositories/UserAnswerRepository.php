<?php

namespace App\Repositories;

use App\Models\UserAnswer;
use App\Repositories\Interfaces\UserAnswerRepositoryInterface;
use Illuminate\Support\Collection;

class UserAnswerRepository implements UserAnswerRepositoryInterface
{
    protected UserAnswer $model;

    public function __construct(UserAnswer $model)
    {
        $this->model = $model;
    }

    public function create(array $data): UserAnswer
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?UserAnswer
    {
        return $this->model->find($id);
    }

    public function getResultByQuizAndUser(int $quizId, int $userId): ?UserAnswer
    {
        return $this->model->where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->first();
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

    public function getCorrectAnswersCount(int $quizId, int $userId): int
    {
        return $this->model->whereHas('result', function ($query) use ($quizId, $userId) {
                $query->where('quiz_id', $quizId)
                      ->where('user_id', $userId);
            })
            ->where('is_correct', true)
            ->count();
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

}
