<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Repositories\Interfaces\QuizRepositoryInterface;

class QuizRepository implements QuizRepositoryInterface
{
    public function create(array $data): Quiz
    {
        return Quiz::create($data);
    }

    public function findById(int $id): ?Quiz
    {
        return Quiz::find($id);
    }

    public function all(): iterable
    {
        return Quiz::all();
    }

    public function update(int $id, array $data): bool
    {
        $quiz = $this->findById($id);
        if ($quiz) {
            return $quiz->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $quiz = $this->findById($id);
        if ($quiz) {
            return $quiz->delete();
        }
        return false;
    }
}
