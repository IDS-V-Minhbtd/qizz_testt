<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface
{
    public function create(array $data): Course;

    /**
     * Create a course with lessons in a transaction
     */
    public function createWithLessons(array $data): Course;

    /**
     * Find a course by ID
     */
    public function findById(int $id): ?Course;

    /**
     * Update a course
     */
    public function update(int $id, array $data): ?Course;

    /**
     * Delete a course
     */
    public function delete(int $id): bool;

    /**
     * Get all courses with pagination
     */
    public function all(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator;

    /**
     * Find courses by creator ID
     */
    public function findByCreatorId(int $userId): Collection;

    /**
     * Get all courses with relations
     */
    public function allWithRelations(array $relations = [], int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator;

    /**
     * Find a course by ID with relations
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Course;

    /**
     * Get tags for a course
     */
    public function getTags(int $courseId): Collection;

    /**
     * Get all fields
     */
    public function getFields(): Collection;
}
