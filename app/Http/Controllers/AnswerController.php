<?php
namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Services\AnswerService;

class AnswerController extends Controller
{
    protected $service;

    public function __construct(AnswerService $service)
    {
        $this->service = $service;
    }

    public function create($questionId)
    {
        $question = $this->service->getQuestionById($questionId);
        if (!$question) {
            abort(404, 'Question not found');
        }

        return view('admin.quizz.question.answers.create', compact('question','quizId'));
    }
    public function store(AnswerRequest $request, $questionId)
    {
        $validatedData = $request->validated();
        $validatedData['question_id'] = $questionId;

        // Gộp xử lý tạo answer trong service
        $this->service->create($validatedData);

        return redirect()->route('admin.quizzes.edit', $questionId)
            ->with('success', 'Đáp án đã được thêm!');
    }
    public function edit($questionId, $answerId)
    {
        $question = $this->service->getQuestionById($questionId);
        $answer = $this->service->getAnswerById($answerId);

        if (!$question || !$answer) {
            abort(404, 'Question or Answer not found');
        }

        return view('admin.quizz.question.answers.edit', compact('question', 'answer'));
    }
    public function update(AnswerRequest $request, $questionId, $answerId)
    {
        $validatedData = $request->validated();
        $this->service->update($answerId, $validatedData);

        return redirect()->route('admin.quizzes.edit', $questionId)
            ->with('success', 'Answer updated successfully!');
    }
}