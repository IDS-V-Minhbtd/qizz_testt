<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;

interface CourseRepositoryInterface
{
    public function create(array $data): Course;
    public function findById(int $id): ?Course;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function all(): iterable;
    public function findByCreatorId(int $userId): iterable;
}
