<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;

class CourseService
{
    protected CourseRepositoryInterface $courseRepo;
    protected TagRepositoryInterface $tagRepo;

    public function __construct(CourseRepositoryInterface $courseRepo, TagRepositoryInterface $tagRepo)
    {
        $this->courseRepo = $courseRepo;
        $this->tagRepo = $tagRepo;
    }

    /**
     * Get all courses with related data.
     */
    public function getAllCourses()
    {
        return $this->courseRepo->allWithRelations(['tag', 'creator', 'lessons']);
    }

    /**
     * Get all tags.
     */
    public function getAllTags()
    {
        return $this->tagRepo->all();
    }

    /**
     * Get a course by ID with related data.
     */
    public function getById(int $id)
    {
        return $this->courseRepo->findByIdWithRelations($id, ['tag', 'creator', 'lessons']);
    }

    /**
     * Create a new course.
     */
    public function create(array $data)
    {
        return $this->courseRepo->create($data);
    }

    /**
     * Update an existing course.
     */
    public function update(int $id, array $data)
    {
        return $this->courseRepo->update($id, $data);
    }

    /**
     * Delete a course.
     */
    public function delete(int $id)
    {
        return $this->courseRepo->delete($id);
    }

    /**
     * Get courses by creator ID.
     */
    public function getByCreatorId(int $userId)
    {
        return $this->courseRepo->findByCreatorId($userId);
    }
}