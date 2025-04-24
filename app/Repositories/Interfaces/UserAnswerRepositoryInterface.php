<?php
namespace App\Repositories\Interfaces;

use App\Models\UserAnswer;

interface UserAnswerRepositoryInterface
{
    public function create(array $data): UserAnswer;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}