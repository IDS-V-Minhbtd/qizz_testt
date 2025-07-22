<?php

namespace App\Repositories\Interfaces;

use App\Models\Lesson;

interface LessonRepositoryInterface
{
    public function create(array $data): Lesson;
    public function findById(int $id): ?Lesson;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function all(): iterable;
    public function findByCourseId(int $courseId): iterable;
}
