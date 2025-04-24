<?php

namespace App\Repositories;

use App\Models\Result;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultRepository implements ResultRepositoryInterface
{
    protected Model $model;

    public function __construct(Result $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Result
    {
        return $this->model->create($data);
    }
    public function findbyId(int $id): ?Result
    {
        return $this->model->find($id);
    }
    public function delete(int $id): bool
    {
        $result = $this->findbyId($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}