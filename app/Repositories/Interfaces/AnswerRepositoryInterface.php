<?php 

namespace App\Repositories\Interfaces;

use App\Models\Answer;

interface  AnswerRepositoryInterface
{
    public function create(array $data): Answer;

    public function findById(int $id): ?Answer;

    public function update(int $id, array $data): bool;

    public function all(): iterable;

    public function getByQuestionId($questionId);

    public function deleteByQuestionId(int $questionId): bool;

    public function deleteById(int $id): bool;
}