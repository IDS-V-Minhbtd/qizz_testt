<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuestionService;
use App\Services\AnswerService;
use App\Http\Requests\QuestionRequest;

class QuestionController extends Controller
{
    protected $questionService;
    protected $answerService;

    public function __construct(QuestionService $questionService, AnswerService $answerService)
    {
        $this->questionService = $questionService;
        $this->answerService = $answerService;
    }

    public function create($quizId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        $questions = $this->questionService->getByQuizId($quizId);

        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        return view('admin.quizzes.question.create', compact('quiz', 'questions'));
    }

    public function store(QuestionRequest $request, $quizId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;
        if ($validatedData['answer_type'] === 'multiple_choice') {
            $answers = $request->input('answers');
            $this->questionService->createWithAnswers($validatedData, $answers);
        } elseif ($validatedData['answer_type'] === 'true_false') {
            $correctAnswer = $request->input('correct_answer');
            $this->questionService->createTrueFalse($validatedData, $correctAnswer);
        } elseif ($validatedData['answer_type'] === 'text_input') {
            $textAnswer = $request->input('text_answer');
            $this->questionService->createTextInput($validatedData, $textAnswer);
        }
        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Câu hỏi đã được thêm thành công!');
    }

    public function edit($quizId, $questionId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        $question = $this->questionService->getByQuizIdAndQuestionId($quizId, $questionId);

        if (!$quiz || !$question) {
            abort(404, 'Quiz or Question not found');
        }

        return view('admin.quizzes.question.edit', compact('quiz', 'question'));
    }

    public function update(QuestionRequest $request, $quizId, $questionId)
    {
        $validatedData = $request->validated();
        $this->questionService->update($questionId, $validatedData);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Cập nhật câu hỏi thành công!');
    }

    public function destroy($quizId, $questionId)
    {
        $this->questionService->delete($questionId);

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', 'Xóa câu hỏi thành công!');
    }
}
