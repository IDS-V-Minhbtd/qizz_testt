<?php

namespace App\Services;

use App\Repositories\Interfaces\LessonRepositoryInterface;

class LessonService
{
    protected LessonRepositoryInterface $lessonRepo;

    public function __construct(LessonRepositoryInterface $lessonRepo)
    {
        $this->lessonRepo = $lessonRepo;
    }

    public function create(array $data)
    {
        return $this->lessonRepo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->lessonRepo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->lessonRepo->delete($id);
    }

    public function getById(int $id)
    {
        return $this->lessonRepo->findById($id);
    }

    public function getAll()
    {
        return $this->lessonRepo->all();
    }

    public function getByCourseId(int $courseId)
    {
        return $this->lessonRepo->findByCourseId($courseId);
    }
}
