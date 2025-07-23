<?php

namespace App\Services;

use App\Repositories\Interfaces\LessonRepositoryInterface;

class LessonService
{
    protected $lessonRepo;

    public function __construct(LessonRepositoryInterface $lessonRepo)
    {
        $this->lessonRepo = $lessonRepo;
    }

    public function getAll()
    {
        return $this->lessonRepo->all();
    }

    public function getById($id)
    {
        return $this->lessonRepo->find($id);
    }

    public function create(array $data)
    {
        return $this->lessonRepo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->lessonRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->lessonRepo->delete($id);
    }

    public function getByCourseId($courseId)
    {
        return $this->lessonRepo->findByCourseId($courseId);
    }
}
