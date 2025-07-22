<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class CourseRepository implements CourseRepositoryInterface
{
    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function findById(int $id): ?Course
    {
        return Course::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $course = $this->findById($id);
        if ($course) {
            return $course->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $course = $this->findById($id);
        if ($course) {
            return $course->delete();
        }
        return false;
    }

    public function all(): iterable
    {
        return Course::all();
    }

    public function findByCreatorId(int $userId): iterable
    {
        return Course::where('created_by', $userId)->get();
    }
}
