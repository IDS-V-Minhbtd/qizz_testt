<?php

namespace App\Repositories\Interfaces;

use App\Models\Tag;

interface TagRepositoryInterface
{
    public function all();
    public function findById($id): ?Tag;
    public function create(array $data): Tag;
    public function update($id, array $data): bool;
    public function delete($id): bool;
}
