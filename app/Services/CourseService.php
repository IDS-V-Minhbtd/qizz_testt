<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;

class CourseService
{
    protected CourseRepositoryInterface $courseRepo;

    public function __construct(CourseRepositoryInterface $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    public function create(array $data)
    {
        return $this->courseRepo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->courseRepo->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->courseRepo->delete($id);
    }

    public function getById(int $id)
    {
        return $this->courseRepo->findById($id);
    }

    public function getAll()
    {
        return $this->courseRepo->all();
    }

    public function getByCreatorId(int $userId)
    {
        return $this->courseRepo->findByCreatorId($userId);
    }
}
