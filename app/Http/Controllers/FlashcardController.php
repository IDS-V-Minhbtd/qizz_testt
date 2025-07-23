<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FlashcardService;

class FlashcardController extends Controller
{
    protected $flashcardService;

    public function __construct(FlashcardService $flashcardService)
    {
        $this->flashcardService = $flashcardService;
    }

    // Danh sách flashcard theo lesson
    public function index($lessonId = null)
    {
        if ($lessonId) {
            $flashcards = $this->flashcardService->getByLessonId($lessonId);
            return view('flashcards.index', compact('flashcards', 'lessonId'));
        }
        $flashcards = $this->flashcardService->getAll();
        return view('flashcards.index', compact('flashcards'));
    }

    // Hiển thị form tạo flashcard cho lesson
    public function create($lessonId)
    {
        return view('flashcards.create', compact('lessonId'));
    }

    // Lưu flashcard mới cho lesson
    public function store(Request $request, $lessonId)
    {
        $data = $request->validate([
            'front' => 'required|string',
            'back' => 'required|string',
        ]);
        $data['lesson_id'] = $lessonId;
        $this->flashcardService->create($data);
        return redirect()->route('admin.lessons.flashcards.index', $lessonId)->with('success', 'Tạo flashcard thành công!');
    }

    // Hiển thị form sửa flashcard cho lesson
    public function edit($lessonId, $id)
    {
        $flashcard = $this->flashcardService->getById($id);
        return view('flashcards.edit', compact('flashcard', 'lessonId'));
    }

    // Cập nhật flashcard cho lesson
    public function update(Request $request, $lessonId, $id)
    {
        $data = $request->validate([
            'front' => 'required|string',
            'back' => 'required|string',
        ]);
        $data['lesson_id'] = $lessonId;
        $this->flashcardService->update($id, $data);
        return redirect()->route('admin.lessons.flashcards.index', $lessonId)->with('success', 'Cập nhật flashcard thành công!');
    }

    // Xóa flashcard theo lesson
    public function destroy($lessonId, $id)
    {
        $this->flashcardService->delete($id);
        return redirect()->route('admin.lessons.flashcards.index', $lessonId)->with('success', 'Xóa flashcard thành công!');
    }
}
