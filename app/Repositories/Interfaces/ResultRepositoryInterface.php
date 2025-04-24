<?php
namespace App\Repositories\Interfaces;
use App\Models\Result;

interface ResultRepositoryInterface
{
    public function create(array $data): Result;
    public function findById(int $id): ?Result;
    public function getByUser(int $userId): iterable;
}