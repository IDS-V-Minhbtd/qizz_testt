<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestionService;
use App\Http\Requests\QuestionRequest;

class QuestionController extends Controller
{
    protected $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
    }

    /**
     * Hiển thị form thêm câu hỏi mới cho quiz
     */
    public function create($quizId)
    {
        $quiz = $this->service->getQuizById($quizId);
        $questions = $this->service->getByQuizId($quizId);

        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        return view('AdminQuestions.create', compact('quiz', 'questions'));
    }

    /**
     * Lưu câu hỏi mới vào quiz
     */
    public function store(QuestionRequest $request, $quizId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;

        $this->service->create($validatedData);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Question added successfully!');
    }

    /**
     * Hiển thị form chỉnh sửa một câu hỏi cụ thể
     */
    public function edit($quizId, $questionId)
    {
        $quiz = $this->service->getQuizById($quizId);
        $question = $this->service->getByQuizIdAndQuestionId($quizId, $questionId);

        if (!$quiz || !$question) {
            abort(404, 'Quiz or Question not found');
        }

        return view('AdminQuestions.edit', compact('quiz', 'question'));
    }

    /**
     * Cập nhật câu hỏi sau khi chỉnh sửa
     */
    public function update(QuestionRequest $request, $quizId, $questionId)
    {
        $validatedData = $request->validated();
        $this->service->update($questionId, $validatedData);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Xóa một câu hỏi khỏi quiz
     */
    public function destroy($quizId, $questionId)
    {
        $this->service->delete($questionId);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Question deleted successfully!');
    }
}
