<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Repositories\Interfaces\LessonRepositoryInterface;

class LessonRepository implements LessonRepositoryInterface
{
    public function all()
    {
        return Lesson::all();
    }

    public function find($id)
    {
        return Lesson::findOrFail($id);
    }

    public function create(array $data)
    {
        return Lesson::create($data);
    }

    public function update($id, array $data)
    {
        $lesson = $this->find($id);
        $lesson->update($data);
        return $lesson;
    }

    public function delete($id)
    {
        $lesson = $this->find($id);
        return $lesson->delete();
    }

    public function findByCourseId($courseId)
    {
        return Lesson::where('course_id', $courseId)->orderBy('order_index')->get();
    }
}
