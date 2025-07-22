<?php

namespace App\Repositories\Interfaces;

use App\Models\Progress;

interface ProgressRepositoryInterface
{
    public function create(array $data): Progress;
    public function findById(int $id): ?Progress;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function all(): iterable;
    public function findByUserId(int $userId): iterable;
    public function findByCourseId(int $courseId): iterable;
    public function findByLessonId(int $lessonId): iterable;
}
