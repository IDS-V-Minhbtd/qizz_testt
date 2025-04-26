<?php 

namespace App\Repositories\Interfaces;

use App\Repositories\AnswerRepository;

interface  AnswerRepositoryInterface
{
    public function create(array $data): Answer;

    public function findById(int $id): ?Answer;

    public function update(int $id, array $data): bool;

    public function all(): iterable;
}