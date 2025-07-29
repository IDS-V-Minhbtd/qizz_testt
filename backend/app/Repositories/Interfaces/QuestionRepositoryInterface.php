<?php

namespace App\Repositories\Interfaces;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator; // <-- Add this line

interface QuestionRepositoryInterface
{
    public function create(array $data): Question;
    public function findById(int $id): ?Question;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function all(): iterable;
    public function findByQuizId(int $quizId): iterable;
    public function findByQuizIdAndQuestionId(int $quizId, int $questionId): ?Question;
    public function paginateByQuizId(int $quizId, int $perPage = 10): LengthAwarePaginator;
    public function countByQuizId(int $quizId): int;
}