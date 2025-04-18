<?php

namespace App\Repositories\Interfaces;

use App\Models\Quiz;

interface QuizRepositoryInterface
{
    public function create(array $data): Quiz;
    public function findById(int $id): ?Quiz;
    public function all(): iterable;
}
