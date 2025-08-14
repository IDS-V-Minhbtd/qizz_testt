<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Tag;
use App\Models\Field;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseRepository implements CourseRepositoryInterface
{
    /**
     * Create a new course
     */
    public function create(array $data): Course
    {
        return Course::create($data);
    }

    /**
     * Create a course with lessons in a transaction
     */
    public function createWithLessons(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $courseData = [
                'name' => $data['name'],
                'description' => $data['description'],
                'image' => $data['image'] ?? null,
                'created_by' => $data['created_by'],
                'level' => $data['level'],
                'target' => $data['target'],
                'requirement' => $data['requirement'],
                'field_id' => $data['field_id'],
                'status' => 'pending'
            ];

            $course = Course::create($courseData);

            if (!empty($data['tags'])) {
                $course->tags()->sync($data['tags']);
            }

            foreach ($data['sections'] as $section) {
                foreach ($section['lessons'] as $lessonData) {
                    $course->lessons()->create([
                        'title' => $lessonData['title'],
                        'content' => $lessonData['content'],
                        'resource' => $lessonData['resource'] ?? null,
                        'assignment' => $lessonData['assignment'] ?? null,
                        'youtube_url' => $lessonData['youtube_url'] ?? null,
                        'order_index' => $lessonData['order_index']
                    ]);
                }
            }

            return $course;
        });
    }

    /**
     * Find a course by ID
     */
    public function findById(int $id): ?Course
    {
        return Course::find($id);
    }

    /**
     * Update a course
     */
    public function update(int $id, array $data): ?Course
    {
        $course = $this->findById($id);
        if ($course) {
            $course->update($data);
            if (!empty($data['tags'])) {
                $course->tags()->sync($data['tags']);
            }
            return $course;
        }
        return null;
    }

    /**
     * Delete a course
     */
    public function delete(int $id): bool
    {
        $course = $this->findById($id);
        if ($course) {
            return $course->delete();
        }
        return false;
    }

    /**
     * Get all courses with pagination
     */
    public function all(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Course::paginate($perPage);
    }

    /**
     * Get courses by creator ID
     */
    public function findByCreatorId(int $userId): Collection
    {
        return Course::where('created_by', $userId)->get();
    }

    /**
     * Get all courses with relations
     */
    public function allWithRelations(array $relations = [], int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Course::with($relations)->paginate($perPage);
    }

    /**
     * Find a course by ID with relations
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Course
    {
        return Course::with($relations)->find($id);
    }

    /**
     * Get tags for a course
     */
    public function getTags(int $courseId): Collection
    {
        $course = $this->findById($courseId);
        if ($course) {
            return $course->tags()->get(['tags.id', 'tags.name']);
        }
        return collect([]);
    }

    /**
     * Get all fields
     */
    public function getFields(): Collection
    {
        return Field::select('id', 'name', 'tag_id')->get();
    }

    public function filterByAttributes(array $filters, int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
{
    $query = Course::query();
    if (!empty($filters['level'])) {
        $query->where('level', $filters['level']);
    }
    if (!empty($filters['field_id'])) {
        $query->where('field_id', $filters['field_id']);
    }
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    return $query->paginate($perPage);
}
}