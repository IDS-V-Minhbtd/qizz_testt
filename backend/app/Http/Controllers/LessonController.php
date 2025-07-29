<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Services\LessonService;
use App\Models\Course;

class LessonController extends Controller
{
    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    public function create()
    {
        $courses = Course::all();
        $courseId = request('course_id');
        return view('admin.course.lesson.create', compact('courses', 'courseId'));
    }

    public function store(LessonRequest $request)
    {
        $lesson = $this->lessonService->create($request->validated());
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Tạo bài học thành công!');
    }

    public function edit($id)
    {
        $lesson = $this->lessonService->getById($id);
        $courses = Course::all();
        return view('admin.lesson.edit', compact('lesson', 'courses'));
    }

    public function update(LessonRequest $request, $id)
    {
        $lesson = $this->lessonService->update($id, $request->validated());
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Cập nhật bài học thành công!');
    }

    public function destroy($id)
    {
        $lesson = $this->lessonService->getById($id);
        $this->lessonService->delete($id);
        return redirect()->route('admin.courses.edit', $lesson->course_id)->with('success', 'Xóa bài học thành công!');
    }
}
