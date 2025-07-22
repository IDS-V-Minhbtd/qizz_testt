<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Models\Course;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = $this->courseService->getAll();
        return view('course.index', compact('courses'));
    }

    public function create()
    {
        return view('course.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'created_by' => 'nullable|exists:users,id',
            'tag_id' => 'nullable|exists:tags,id',
            'image' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:courses,slug',
        ]);
        $this->courseService->create($data);
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function show($id)
    {
        $course = $this->courseService->getById($id);
        return view('course.show', compact('course'));
    }

    public function edit($id)
    {
        $course = $this->courseService->getById($id);
        return view('course.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tag_id' => 'nullable|exists:tags,id',
            'image' => 'nullable|string|max:255',
            'slug' => 'required|string|unique:courses,slug,' . $id,
        ]);
        $this->courseService->update($id, $data);
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $this->courseService->delete($id);
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
