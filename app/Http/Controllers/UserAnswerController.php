<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserAnswerService;
use App\Services\QuizService;
use App\Services\QuestionService;
use Illuminate\Support\Facades\Auth;
use App\Models\Quiz; // Add this line to import the Quiz model

class UserAnswerController extends Controller
{
    protected UserAnswerService $userAnswerService;
    protected QuizService $quizService;

    public function __construct(UserAnswerService $userAnswerService, QuizService $quizService)
    {
        $this->userAnswerService = $userAnswerService;
        $this->quizService = $quizService;
    }

    // Hiển thị trang làm quiz (danh sách câu hỏi)
    public function showQuiz($quizId)
    {
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz || $quiz->status !== 'public') {
            return redirect()->route('dashboard')->with('error', 'Quiz không khả dụng.');
        }

        $questions = $quiz->questions()->with('answers')->orderBy('order')->get();

        return view('quiz.play', compact('quiz', 'questions'));
    }

    // Gửi câu trả lời
    public function submit(Request $request, $quizId)
    {
        $userId = Auth::id();
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz) {
            return redirect()->route('dashboard')->with('error', 'Quiz không tồn tại.');
        }

        $this->userAnswerService->deleteAnswers($quizId, $userId);

        $answers = $request->input('answers'); // dạng: [question_id => answer_id]

        foreach ($answers as $questionId => $answerId) {
            $this->userAnswerService->saveAnswer([
                'quiz_id'     => $quizId,
                'question_id' => $questionId,
                'user_id'     => $userId,
                'answer_id'   => $answerId,
                'is_correct'  => $this->userAnswerService->isCorrect($questionId, $answerId),
            ]);
        }

        return redirect()->route('quiz.result', $quizId)->with('success', 'Nộp bài thành công!');
    }

    public function start(Quiz $quiz)
    {
        if (!$quiz->is_public) {
            return redirect()->route('home')->with('error', 'Quiz này không công khai.');
        }

        $questions = $quiz->questions()->with('answers')->get();
        return view('quizz.index', compact('quiz', 'questions'));
    }

    // Hiển thị kết quả quiz
    public function result($quizId)
    {
        $userId = Auth::id();
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz) {
            return redirect()->route('dashboard')->with('error', 'Quiz không tồn tại.');
        }

        $userAnswers = $this->userAnswerService->getAnswersByQuiz($quizId, $userId);
        $correctCount = $this->userAnswerService->getCorrectCount($quizId, $userId);
        $totalQuestions = $quiz->questions()->count();

        return view('quiz.result', compact('quiz', 'userAnswers', 'correctCount', 'totalQuestions'));
    }
}
