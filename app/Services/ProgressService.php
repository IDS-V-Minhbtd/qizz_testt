<?php

namespace App\Services;

use App\Repositories\Interfaces\ProgressRepositoryInterface;

class ProgressService
{
    protected ProgressRepositoryInterface $progressRepo;

    public function __construct(ProgressRepositoryInterface $progressRepo)
    {
        $this->progressRepo = $progressRepo;
    }

    public function create(array $data)
    {
        return $this->progressRepo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->progressRepo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->progressRepo->delete($id);
    }

    public function getById(int $id)
    {
        return $this->progressRepo->findById($id);
    }

    public function getAll()
    {
        return $this->progressRepo->all();
    }

    public function getByUserId(int $userId)
    {
        return $this->progressRepo->findByUserId($userId);
    }

    public function getByCourseId(int $courseId)
    {
        return $this->progressRepo->findByCourseId($courseId);
    }

    public function getByLessonId(int $lessonId)
    {
        return $this->progressRepo->findByLessonId($lessonId);
    }
}
