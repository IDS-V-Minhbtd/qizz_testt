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

    public function create($quizId)
    {
        $quiz = $this->service->getQuizById($quizId);
        $questions = $this->service->getByQuizId($quizId);

        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        return view('admin.quizzes.question.create', compact('quiz', 'questions'));
    }

    public function store(QuestionRequest $request, $quizId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;

        // Gộp xử lý tạo question + answers trong service
        $this->service->createWithAnswers($validatedData);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Câu hỏi và các đáp án đã được thêm!');
    }

    public function edit($quizId, $questionId)
    {
        $quiz = $this->service->getQuizById($quizId);
        $question = $this->service->getByQuizIdAndQuestionId($quizId, $questionId);

        if (!$quiz || !$question) {
            abort(404, 'Quiz or Question not found');
        }

        return view('admin.quizzes.question.edit', compact('quiz', 'question'));
    }

    public function update(QuestionRequest $request, $quizId, $questionId)
    {
        $validatedData = $request->validated();
        $this->service->update($questionId, $validatedData);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy($quizId, $questionId)
    {
        $this->service->delete($questionId);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Question deleted successfully!');
    }
}
