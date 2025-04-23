<?php 

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findById(int $id): ?User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function all(): iterable;
}