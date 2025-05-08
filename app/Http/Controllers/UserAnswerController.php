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
        if ($request->method() !== 'POST') {
            abort(405, 'Method Not Allowed');
        }

        $userId = Auth::id();
        $quiz = $this->quizService->getById($quizId);

        if (!$quiz) {
            return redirect()->route('dashboard')->with('error', 'Quiz không tồn tại.');
        }

        $this->userAnswerService->deleteAnswers($quizId, $userId);

        $answers = $request->input('answers');

        // Nếu là chuỗi JSON, parse về mảng
        if (is_string($answers)) {
            $answers = json_decode($answers, true);
        }

        // Nếu không phải mảng sau khi parse, trả về lỗi
        if (!is_array($answers)) {
            return redirect()->back()->with('error', 'Dữ liệu câu trả lời không hợp lệ.');
        }

        foreach ($answers as $questionId => $answerId) {
            // Nếu $answerId là mảng (ví dụ: [2]), lấy phần tử đầu tiên
            if (is_array($answerId)) {
                $answerId = $answerId[0] ?? null;
            }
        
            if (!is_int($answerId)) {
                continue; // hoặc xử lý lỗi
            }
        
            $this->userAnswerService->saveAnswer([
                'quiz_id'     => $quizId,
                'question_id' => (int) $questionId,
                'user_id'     => $userId,
                'answer_id'   => $answerId,
                'is_correct'  => $this->userAnswerService->isCorrect((int) $questionId, $answerId),
            ]);
        }
        

        return redirect()->route('quizz.submit', $quizId)->with('success', 'Nộp bài thành công!');
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
