<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function findByUsername(string $username): ?User;

    public function findProfile(int $id): ?User;
}
