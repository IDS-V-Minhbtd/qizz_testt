<?php

namespace App\Repositories;

use App\Models\Progress;
use App\Repositories\Interfaces\ProgressRepositoryInterface;

class ProgressRepository implements ProgressRepositoryInterface
{
    public function create(array $data): Progress
    {
        return Progress::create($data);
    }

    public function findById(int $id): ?Progress
    {
        return Progress::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $progress = $this->findById($id);
        if ($progress) {
            return $progress->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $progress = $this->findById($id);
        if ($progress) {
            return $progress->delete();
        }
        return false;
    }

    public function all(): iterable
    {
        return Progress::all();
    }

    public function findByUserId(int $userId): iterable
    {
        return Progress::where('user_id', $userId)->get();
    }

    public function findByCourseId(int $courseId): iterable
    {
        return Progress::where('course_id', $courseId)->get();
    }

    public function findByLessonId(int $lessonId): iterable
    {
        return Progress::where('lesson_id', $lessonId)->get();
    }
}
