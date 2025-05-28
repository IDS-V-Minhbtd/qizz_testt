<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Services\QuestionService;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function __construct(protected QuestionService $questionService)
    {
    }

    public function create($quizId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        $questions = $this->questionService->getByQuizId($quizId);

        return view('admin.quizzes.question.create', compact('quiz', 'questions'));
    }

    public function store(QuestionRequest $request, $quizId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;

        Log::info('Dữ liệu đã validate trước khi gửi tới QuestionService:', $validatedData);

        try {
            $this->questionService->createWithAnswers($validatedData);
            return redirect()->route('admin.quizzes.edit', $quizId)
                ->with('success', 'Câu hỏi đã được thêm thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Không thể tạo câu hỏi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($quizId, $questionId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        $question = $this->questionService->getByQuizIdAndQuestionId($quizId, $questionId);

        if (!$quiz || !$question) {
            abort(404, 'Quiz or question not found');
        }

        $answersRaw = $this->questionService->getAnswerByQuestionId($questionId) ?? collect();
        $answers = $answersRaw->map(function($item) {
            return [
                'id' => $item->id,
                'answer' => $item->answer,
                'text' => $item->answer,
                'is_correct' => (bool) $item->is_correct,
            ];
        });

        Log::info('Question edit data:', [
            'quiz' => $quiz->toArray(),
            'question' => $question->toArray(),
            'answers' => $answers->toArray(),
        ]);

        return view('admin.quizzes.question.edit', compact('quiz', 'question', 'answers'));
    }

    public function update(QuestionRequest $request, $quizId, $questionId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;

        Log::info('Dữ liệu gửi từ form (update):', $validatedData);

        try {
            $this->questionService->updateWithAnswers($questionId, $validatedData);
            return redirect()->route('admin.quizzes.edit', $quizId)
                ->with('success', 'Cập nhật câu hỏi thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Không thể cập nhật câu hỏi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($quizId, $questionId)
    {
        try {
            $this->questionService->delete($questionId);
            return redirect()->route('admin.quizzes.edit', $quizId)
                ->with('success', 'Xóa câu hỏi thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Không thể xóa câu hỏi: ' . $e->getMessage()]);
        }
    }
}