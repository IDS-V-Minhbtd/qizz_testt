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

    public function getUserAnswerByQuizAndQuestion(int $quizId, int $questionId, int $userId): ?UserAnswer;

    public function getCorrectAnswersCount(int $quizId, int $userId): int;

    public function getAllAnswersByQuiz(int $quizId, int $userId): Collection;

    public function deleteAnswers(int $quizId, int $userId): void;

    public function CheckCorrectAnswer(int $questionId, int $answerId): bool;
}
