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
}
    

