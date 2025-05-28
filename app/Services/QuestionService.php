<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;

class QuestionService
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected AnswerRepositoryInterface $answerRepo
    ) {}

    public function createQuestion(array $data): Question
    {
        Log::info('Tạo câu hỏi:', $data);
        return $this->questionRepo->create([
            'quiz_id'  => $data['quiz_id'],
            'question' => $data['question'],
            'order'    => $data['order'] ?? 0,
            'type'     => $data['answer_type'],
        ]);
    }

    public function updateQuestion(int $questionId, array $data): void
    {
        $this->questionRepo->update($questionId, [
            'quiz_id'  => $data['quiz_id'],
            'question' => $data['question'],
            'order'    => $data['order'] ?? 0,
            'type'     => $data['answer_type'],
        ]);
    }

    public function createAnswersForQuestion(int $questionId, array $data): void
    {
        $answerType = $data['answer_type'] ?? null;

        if ($answerType === 'multiple_choice') {
            $this->validateMultipleChoiceAnswers($data);

            foreach ($data['answers'] as $id => $answer) {
                if (!empty(trim($answer['text'] ?? ''))) {
                    $isCorrect = ((int)$data['correct_answer'] === $id);
                    Log::info('Chuẩn bị tạo đáp án', [
                        'question_id' => $questionId,
                        'answer_text' => $answer['text'],
                        'correct_answer' => $data['correct_answer'],
                        'id' => $id,
                        'is_correct' => $isCorrect
                    ]);
                    try {
                        $this->answerRepo->create([
                            'question_id' => $questionId,
                            'answer' => $answer['text'],
                            'is_correct' => $isCorrect,
                        ]);
                        Log::info('Đáp án đã được lưu', ['answer_text' => $answer['text']]);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi lưu đáp án: ' . $e->getMessage(), [
                            'answer_text' => $answer['text'],
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e; // Ném lỗi để transaction rollback
                    }
                }
            }

        } elseif ($answerType === 'text_input') {
            $this->validateTextInputAnswer($data);

            $this->answerRepo->create([
                'question_id' => $questionId,
                'answer' => $data['text_answer'],
                'is_correct' => true,
            ]);

        } elseif ($answerType === 'true_false') {
            $this->validateTrueFalseAnswer($data);

            $correct = (int)$data['correct_answer'] === 1;
            $this->answerRepo->createMany([
                ['question_id' => $questionId, 'answer' => 'Đúng', 'is_correct' => $correct],
                ['question_id' => $questionId, 'answer' => 'Sai', 'is_correct' => !$correct],
            ]);

        } else {
            throw new Exception('Loại câu hỏi không hợp lệ.');
        }
    }

    public function updateAnswersForQuestion(int $questionId, array $data): void
    {
        $this->answerRepo->deleteByQuestionId($questionId);
        $this->createAnswersForQuestion($questionId, $data);
    }

    public function createWithAnswers(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            $question = $this->createQuestion($data);
            $this->createAnswersForQuestion($question->id, $data);
            return $this->questionRepo->findById($question->id);
        });
    }

    public function updateWithAnswers(int $questionId, array $data): Question
    {
        return DB::transaction(function () use ($questionId, $data) {
            $this->updateQuestion($questionId, $data);
            $this->updateAnswersForQuestion($questionId, $data);
            return $this->questionRepo->findById($questionId);
        });
    }

    public function getById(int $id)
    {
        return $this->questionRepo->findById($id);
    }

    public function delete(int $id)
    {
        return $this->questionRepo->delete($id);
    }

    public function getByQuizId(int $quizId)
    {
        return $this->questionRepo->findByQuizId($quizId); 
    }

    public function getAnswerByQuestionId(int $questionId)
    {
        return $this->answerRepo->getByQuestionId($questionId);
    }

    public function getByQuizIdAndQuestionId(int $quizId, int $questionId)
    {
        return $this->questionRepo->findByQuizIdAndQuestionId($quizId, $questionId);
    }

    public function getQuizById(int $quizId)
    {
        return $this->quizRepo->findById($quizId);
    }

    public function paginateByQuizId(int $quizId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->questionRepo->paginateByQuizId($quizId, $perPage);
    }

    protected function validateMultipleChoiceAnswers(array $data): void
    {
        if (!isset($data['answers']) || count($data['answers']) < 2) {
            throw new Exception('Cần ít nhất hai đáp án.');
        }

        $correctAnswerId = $data['correct_answer'] ?? null;

        if (!$correctAnswerId || !array_key_exists($correctAnswerId, $data['answers'])) {
            throw new Exception('Đáp án đúng không hợp lệ.');
        }
    }

    protected function validateTextInputAnswer(array $data): void
    {
        if (empty(trim($data['text_answer'] ?? ''))) {
            throw new Exception('Đáp án văn bản không được để trống.');
        }
    }

    protected function validateTrueFalseAnswer(array $data): void
    {
        if (!in_array($data['correct_answer'] ?? '', ['0', '1'], true)) {
            throw new Exception('Đáp án đúng cho câu hỏi Đúng/Sai không hợp lệ.');
        }
    }
}