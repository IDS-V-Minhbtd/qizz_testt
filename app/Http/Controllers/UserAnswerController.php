<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserAnswerService;
use App\Services\QuizService;
use App\Services\ResultService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAnswerController extends Controller
{
    protected UserAnswerService $userAnswerService;
    protected QuizService $quizService;
    protected ResultService $resultService;

    public function __construct(
        UserAnswerService $userAnswerService,
        QuizService $quizService,
        ResultService $resultService
    ) {
        $this->userAnswerService = $userAnswerService;
        $this->quizService = $quizService;
        $this->resultService = $resultService;
    }

    /**
     * Hiển thị trang bắt đầu làm quiz
     */
    public function start($quizId)
    {
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz || !$quiz->is_public) {
            return redirect()->route('dashboard')->with('error', 'Quiz không khả dụng.');
        }

        $questions = $quiz->questions()->with('answers')->orderBy('order')->get();

        return view('quizz.index', compact('quiz', 'questions'));
    }

    /**
     * Gửi toàn bộ bài làm và lưu kết quả
     */
    public function submit(Request $request, $quizId)
    {
        $userId = Auth::id();
        $answers = $request->input('answers');
        $timeTaken = $request->input('time_taken'); // Retrieve time_taken from the request

        if (!is_array($answers)) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ.');
        }

        // Pass the timeTaken argument to the service method
        $result = $this->resultService->submitQuizAndSaveResult($quizId, $answers, $userId, $timeTaken);

        return redirect()->route('quizz.result', $result->id)->with('success', 'Nộp bài thành công!');
    }

    /**
     * Hiển thị kết quả quiz theo result_id
     */
    public function result($resultId)
    {
        $result = $this->resultService->getResultById($resultId);

        if (!$result || $result->user_id !== Auth::id()) {
            return redirect()->route('dashboard')->with('error', 'Không tìm thấy kết quả.');
        }

        return view('quizz.result', compact('result'));
    }

    /**
     * Hiển thị kết quả quiz theo quiz_id
     */
    public function resultByQuiz($quizId)
    {
        $userId = Auth::id();

        $result = $this->resultService->getResultWithAnswers($quizId, $userId);

        if (!$result) {
            return redirect()->route('dashboard')->with('error', 'Không tìm thấy kết quả.');
        }

        // Load quan hệ nếu cần
        $result->load('quiz', 'userAnswers.question', 'userAnswers.answer');

        return view('quizz.result', compact('result'));
    }


    /**
     * Kiểm tra đáp án đúng sai (AJAX)
     */
    public function checkAnswer(Request $request)
    {
        $questionId = $request->input('question_id');
        $answerId = $request->input('answer_id');

        $isCorrect = $this->userAnswerService->isCorrect($questionId, $answerId);

        return response()->json(['is_correct' => $isCorrect]);
    }
}
