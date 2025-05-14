<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserAnswerService;
use App\Services\QuizService;
use App\Services\QuestionService;
use Illuminate\Support\Facades\Auth;

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

    public function start($quizId)
    {
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz || !$quiz->is_public) {
            return redirect()->route('dashboard')->with('error', 'Quiz không khả dụng.');
        }

        $questions = $quiz->questions()->with('answers')->orderBy('order')->get();

        return view('quizz.index', compact('quiz', 'questions'));
    }

    // Gửi câu trả lời
    public function submit(Request $request, $quizId)
{
    $userId = Auth::id();
    $answers = $request->input('answers'); // expect: ['question_id' => 'answer_id', ...]

    if (!is_array($answers)) {
        return redirect()->back()->with('error', 'Dữ liệu không hợp lệ.');
    }

    $this->userAnswerService->submitAnswers($quizId, $userId, $answers);

    return redirect()->route('quizz.result', $quizId)->with('success', 'Nộp bài thành công!');
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
        $correctCount = $this->userAnswerService->getCorrectCount($quizId, $userId); // Đảm bảo phương thức này được định nghĩa trong service
        $totalQuestions = $quiz->questions()->count();
    
        return view('quizz.result', compact('quiz', 'userAnswers', 'correctCount', 'totalQuestions'));
    }
    
    public function checkAnswer(Request $request, Quiz $quiz)
    {
        $questionId = $request->input('question_id');
        $answerId = $request->input('answer_id');

        // Kiểm tra câu trả lời
        $isCorrect = $this->userAnswerService->isCorrect($questionId, $answerId);

        return response()->json(['is_correct' => $isCorrect]);
    }
}
