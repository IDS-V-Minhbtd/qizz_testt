<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Repositories\Interfaces\LessonRepositoryInterface;

class LessonRepository implements LessonRepositoryInterface
{
    public function create(array $data): Lesson
    {
        return Lesson::create($data);
    }

    public function findById(int $id): ?Lesson
    {
        return Lesson::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $lesson = $this->findById($id);
        if ($lesson) {
            return $lesson->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $lesson = $this->findById($id);
        if ($lesson) {
            return $lesson->delete();
        }
        return false;
    }

    public function all(): iterable
    {
        return Lesson::all();
    }

    public function findByCourseId(int $courseId): iterable
    {
        return Lesson::where('course_id', $courseId)->orderBy('order')->get();
    }
}
