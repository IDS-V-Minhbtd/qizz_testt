<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Services\CourseService;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = $this->courseService->getAllCourses();
        return view('admin.course.index', compact('courses'));
    }

    public function create()
    {
        $tags = $this->courseService->getAllTags();
        return view('admin.course.create', compact('tags'));
    }

    public function show($id)
    {
        $course = $this->courseService->getById($id);
        return view('course.show', compact('course'));
    }

    public function edit($id)
    {
        $course = $this->courseService->getById($id);
        $tags = $this->courseService->getAllTags();
        return view('admin.course.edit', compact('course', 'tags'));
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $this->courseService->create($data);
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function update(UpdateCourseRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $this->courseService->update($id, $data);
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $this->courseService->delete($id);
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}