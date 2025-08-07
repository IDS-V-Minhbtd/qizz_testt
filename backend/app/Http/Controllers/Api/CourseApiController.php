<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use App\Services\LessonService;
use App\Http\Resources\CourseResource;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Field;

class CourseApiController extends Controller
{
    protected $courseService;
    protected $lessonService;

    public function __construct(CourseService $courseService, LessonService $lessonService)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->courseService = $courseService;
        $this->lessonService = $lessonService;
    }

    /**
     * Get data for creating a course (tags and fields)
     */
    public function create()
    {
        $tags = Tag::select('id', 'name')->get();
        $fields = Field::select('id', 'name', 'tag_id')->get();
        return response()->json([
            'success' => true,
            'data' => [
                'tags' => $tags,
                'fields' => $fields,
                'levels' => ['Beginner', 'Intermediate', 'Expert']
            ]
        ], 200);
    }

    /**
     * Store a new course with lessons
     */
    public function store(CourseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $data['created_by'] = auth()->id();
        $course = $this->courseService->createWithLessons($data);
        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được tạo thành công và đang chờ kiểm duyệt.',
            'data' => new CourseResource($course)
        ], 201);
    }

    /**
     * Get course review status
     */
    public function reviewStatus($id)
    {
        $course = $this->courseService->getById($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'course_id' => $course->id,
                'status' => $course->status
            ]
        ], 200);
    }

    /**
     * Existing methods (index, show, edit, update, destroy) remain unchanged
     */
    public function index()
    {
        $courses = $this->courseService->getAllCourses();
        return CourseResource::collection($courses);
    }

    public function show($id)
    {
        $course = $this->courseService->getById($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại.'
            ], 404);
        }
        return new CourseResource($course);
    }

    public function edit($id)
    {
        $course = $this->courseService->getById($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại.'
            ], 404);
        }
        $tags = $this->courseService->getAllTags();
        $lessons = $this->lessonService->getByCourseId($id);
        return response()->json([
            'success' => true,
            'data' => [
                'course' => new CourseResource($course),
                'tags' => $tags,
                'lessons' => $lessons
            ]
        ], 200);
    }

    public function update(CourseRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course = $this->courseService->update($id, $data);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được cập nhật thành công.',
            'data' => new CourseResource($course)
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->courseService->delete($id);
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại.'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được xóa thành công.'
        ], 200);
    }
}