<?php

namespace App\Repositories\Interfaces;

use App\Models\Quiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuizRepositoryInterface 
{
    public function create(array $data): Quiz;
    public function findById(int $id): ?Quiz;
    public function paginate(int $perPage = 10): LengthAwarePaginator;
    public function all(): iterable;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
