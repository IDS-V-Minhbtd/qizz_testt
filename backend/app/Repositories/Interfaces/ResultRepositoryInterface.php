<?php
namespace App\Repositories\Interfaces;
use App\Models\Result;

interface ResultRepositoryInterface
{
    public function create(array $data): Result;
    public function findById(int $id): ?Result;
    public function getByUser(int $userId): iterable;
    public function findByIdWithAnswers(int $id);
    public function delete(int $id): bool;
    public function getByUserId($userId);
}