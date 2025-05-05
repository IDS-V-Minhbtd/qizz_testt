<?php

namespace App\Repositories\Interfaces;

use App\Models\UserAnswer;
use Illuminate\Support\Collection;

interface UserAnswerRepositoryInterface
{
    public function create(array $data): UserAnswer;

    public function findById(int $id): ?UserAnswer;

    public function getResultByQuizAndUser(int $quizId, int $userId): ?UserAnswer;

    public function getUserResults(int $userId): Collection;
}
