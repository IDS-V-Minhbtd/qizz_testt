<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Http\Requests\ValidateQuizRequest;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Support\Facades\Log;

class QuizController
{
    protected $service;

    public function __construct(QuizService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        // Lấy danh sách tất cả các quiz và trả về view
        $quizzes = $this->service->getAll();
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('quizzes.create');
    }

    public function store(ValidateQuizRequest $request)
    {
        // Xử lý và tạo quiz mới
        $validatedData = $request->validated();

        // Tạo quiz thông qua service
        $this->service->create($validatedData);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully!');
    }

    public function show($id)
    {
        $quiz = $this->service->getById((int) $id); // Đảm bảo rằng ID là kiểu số
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        return view('quizzes.show', compact('quiz'));
    }

    public function edit($id)
    {
        // Lấy quiz theo ID
        $quiz = $this->service->getById($id);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }
    
        // Lấy các câu hỏi liên quan đến quiz
        $questions = $quiz->questions()->orderBy('order')->get();
    
        return view('quizzes.edit', compact('quiz', 'questions'));
    }

    public function update(ValidateQuizRequest $request, $id)
    {
        // Xử lý và cập nhật quiz
        $validatedData = $request->validated();

        // Cập nhật quiz thông qua service
        $this->service->update($id, $validatedData);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz updated successfully!');
    }

    public function destroy($id)
    {
        // Xóa quiz theo ID
        $this->service->delete($id);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    // Thêm câu hỏi cho quiz
    public function storeQuestion(Request $request, $quizId)
    {
        // Validate câu hỏi
        $request->validate([
            'question' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
        ]);

        // Tìm quiz theo ID
        $quiz = Quiz::findOrFail($quizId);

        // Tạo câu hỏi mới
        $question = new Question();
        $question->quiz_id = $quiz->id;
        $question->question = $request->input('question');
        $question->order = $request->input('order');
        $question->answer_type = 'multiple_choice'; // Mặc định là nhiều lựa chọn, có thể thay đổi nếu cần
        $question->save();

        return redirect()->route('admin.quizzes.edit', $quiz->id)->with('success', 'Câu hỏi đã được thêm thành công.');
    }

    // Xóa câu hỏi cho quiz
    public function destroyQuestion($quizId, $questionId)
    {
        // Tìm câu hỏi theo ID
        $question = Question::where('quiz_id', $quizId)->findOrFail($questionId);
        $question->delete();

        return redirect()->route('admin.quizzes.edit', $quizId)->with('success', 'Câu hỏi đã được xóa.');
    }
}
