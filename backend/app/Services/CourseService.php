<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    /**
     * Get all courses with pagination and relations
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllCourses(int $perPage = 10)
    {
        Log::info('Fetching all courses with relations');
        return $this->courseRepository->allWithRelations(['lessons', 'tags', 'field'], $perPage);
    }

    /**
     * Get a course by ID with relations
     *
     * @param int $id
     * @return \App\Models\Course|null
     * @throws \Exception
     */
    public function getById(int $id)
    {
        Log::info("Fetching course with ID: {$id}");
        $course = $this->courseRepository->findByIdWithRelations($id, ['lessons', 'tags', 'field']);
        if (!$course) {
            Log::warning("Course not found with ID: {$id}");
            throw new \Exception('Course not found', 404);
        }
        return $course;
    }

    /**
     * Get all available tags
     *
     * @return Collection
     */
    public function getAllTags(): Collection
    {
        Log::info('Fetching all tags');
        return $this->courseRepository->getTags(0); 
    }

    /**
     * Get all available fields
     *
     * @return Collection
     */
    public function getAllFields(): Collection
    {
        Log::info('Fetching all fields');
        return $this->courseRepository->getFields();
    }

    /**
     * Create a course with lessons and tags
     *
     * @param array $data
     * @return \App\Models\Course
     * @throws \Exception
     */
    public function createWithLessons(array $data)
    {
        Log::info('Creating course with lessons', ['name' => $data['name']]);
        
        // Ensure the user has permission (admin or quizz_master)
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'quizz_master'])) {
            Log::warning('Unauthorized attempt to create course', ['user_id' => $user?->id]);
            throw new \Exception('Unauthorized to create course', 403);
        }

        // Add created_by to data
        $data['created_by'] = $user->id;

        // Validate tags and field_id
        if (!empty($data['tags'])) {
            $validTags = Tag::whereIn('id', $data['tags'])->pluck('id')->toArray();
            if (count($validTags) !== count($data['tags'])) {
                Log::warning('Invalid tags provided', ['tags' => $data['tags']]);
                throw new \Exception('Invalid tags provided', 422);
            }
        }
        if (!Field::where('id', $data['field_id'])->exists()) {
            Log::warning('Invalid field_id provided', ['field_id' => $data['field_id']]);
            throw new \Exception('Invalid field provided', 422);
        }

        // Create course and lessons in a transaction
        $course = $this->courseRepository->createWithLessons($data);
        Log::info('Course created successfully', ['course_id' => $course->id]);
        
        return $course;
    }

    /** 
     * Update a course
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Course|null
     * @throws \Exception
     */
    public function update(int $id, array $data)
    {
        Log::info("Updating course with ID: {$id}");

        // Ensure the user has permission
        $user = auth()->user();
        $course = $this->courseRepository->findById($id);
        if (!$course) {
            Log::warning("Course not found with ID: {$id}");
            throw new \Exception('Course not found', 404);
        }
        if (!$user || ($user->id !== $course->created_by && !in_array($user->role, ['admin']))) {
            Log::warning('Unauthorized attempt to update course', ['user_id' => $user?->id, 'course_id' => $id]);
            throw new \Exception('Unauthorized to update course', 403);
        }

        // Validate tags
        if (!empty($data['tags'])) {
            $validTags = Tag::whereIn('id', $data['tags'])->pluck('id')->toArray();
            if (count($validTags) !== count($data['tags'])) {
                Log::warning('Invalid tags provided', ['tags' => $data['tags']]);
                throw new \Exception('Invalid tags provided', 422);
            }
        }

        $updatedCourse = $this->courseRepository->update($id, $data);
        if (!$updatedCourse) {
            Log::warning("Failed to update course with ID: {$id}");
            throw new \Exception('Failed to update course', 500);
        }
        Log::info("Course updated successfully", ['course_id' => $id]);
        
        return $updatedCourse;
    }

    /**
     * Delete a course
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id)
    {
        Log::info("Deleting course with ID: {$id}");

        // Ensure the user has permission
        $user = auth()->user();
        $course = $this->courseRepository->findById($id);
        if (!$course) {
            Log::warning("Course not found with ID: {$id}");
            throw new \Exception('Course not found', 404);
        }
        if (!$user || ($user->id !== $course->created_by && !in_array($user->role, ['admin']))) {
            Log::warning('Unauthorized attempt to delete course', ['user_id' => $user?->id, 'course_id' => $id]);
            throw new \Exception('Unauthorized to delete course', 403);
        }

        $deleted = $this->courseRepository->delete($id);
        if (!$deleted) {
            Log::warning("Failed to delete course with ID: {$id}");
            throw new \Exception('Failed to delete course', 500);
        }
        Log::info("Course deleted successfully", ['course_id' => $id]);
        
        return true;
    }

    /**
     * Get tags for a course
     *
     * @param int $courseId
     * @return Collection
     */
    public function getCourseTags(int $courseId): Collection
    {
        return $this->courseRepository->getTags($courseId);
    }
}