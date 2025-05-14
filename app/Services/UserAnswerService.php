<?php

namespace App\Services;
 
use App\Repositories\Interfaces\{
    UserAnswerRepositoryInterface,
    AnswerRepositoryInterface,
    QuestionRepositoryInterface,
    QuizRepositoryInterface,
    ResultRepositoryInterface
};
use Illuminate\Support\Facades\Auth;

class UserAnswerService
{
    public function __construct(
        protected UserAnswerRepositoryInterface $userAnswerRepo,
        protected AnswerRepositoryInterface $answerRepo,
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected ResultRepositoryInterface $resultRepo
    ) {}

   public function submitQuiz(int $quizId, array $answers, int $userId): array
{
    // Lấy quiz
    $quiz = $this->quizRepo->findById($quizId);
    if (!$quiz || !$quiz->is_public) {
        return [
            'success' => false,
            'message' => 'Quiz không khả dụng.'
        ];
    }

    // Create a result entry for the user
    $result = $this->resultRepo->create([
        'user_id'     => $userId,
        'quiz_id'     => $quizId,
        'score'       => 0, // Initial score, will be updated later
        'time_taken'  => 0, // Placeholder, update as needed
        'completed_at'=> now(),
    ]);
    

    foreach ($answers as $questionId => $answerId) {
        if (is_array($answerId)) {
            $answerId = $answerId[0] ?? null;
        }

        if (!is_numeric($answerId)) {
            continue;
        }

        $userAnswer = $this-> userAnswerRepo->create([
            'user_id'     => $userId,
            'quiz_id'     => $quizId,
            'result_id'   => $result->id,
            'question_id' => $questionId,
            'answer'      => $answerId,  
            'answer_id'   => $answerId,
            'is_correct'  => $this->isCorrect($questionId, $answerId),
        ]);
    }

    return [
        'success' => true,
        'message' => 'Nộp bài thành công.',
        'result_id' => $result->id
    ];
}


    public function getQuizWithQuestions(int $quizId)
    {
        return $this->quizRepo->findByIdWithQuestions($quizId);
    }


    public function saveAnswer(array $data): void
    {
        $this->userAnswerRepo->create([
            'result_id'   => $data['result_id'],
            'question_id' => $data['question_id'],
            'answer'      => $data['answer'] ?? '', // Provide a default value if 'answer' is not set
            'is_correct'  => $data['is_correct'],
        ]);
    }

    public function isCorrect(int $questionId, int $answerId): bool
    {
        $answer = $this->answerRepo->findById($answerId);
        return $answer && $answer->question_id === $questionId && $answer->is_correct;
    }

    public function getAnswersByQuiz(int $quizId, int $userId)
    {
        return $this->userAnswerRepo->getAllAnswersByQuiz($quizId, $userId);
    }

    public function getCorrectCount(int $quizId, int $userId): int
    {
        $answers = $this->userAnswerRepo->getAllAnswersByQuiz($quizId, $userId);

        return $answers->filter(function ($answer) {
            return $answer->is_correct;
        })->count();
    }
}
