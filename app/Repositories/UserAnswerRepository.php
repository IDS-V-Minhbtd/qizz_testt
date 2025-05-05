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
}
