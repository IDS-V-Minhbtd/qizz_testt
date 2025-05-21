<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionService
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected AnswerRepositoryInterface $answerRepo
    ) {}

    public function create(array $data)
    {
        return $this->questionRepo->create($data);
    }

    public function getById(int $id)
    {
        return $this->questionRepo->findById($id);
    }

    public function update(int $id, array $data)
    {
        return $this->questionRepo->update($id, $data);
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

    public function createWithAnswers(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            \Log::info('Dữ liệu đầu vào createWithAnswers:', $data);

            $question = $this->questionRepo->create([
                'quiz_id'  => $data['quiz_id'],
                'question' => $data['question'],
                'order'    => $data['order'] ?? 0,
                'type'     => $data['answer_type'],
            ]);

            if ($data['answer_type'] === 'multiple_choice') {
                if (!isset($data['answers']) || !is_array($data['answers']) || count($data['answers']) < 2) {
                    throw new \Exception('Cần ít nhất hai đáp án cho câu hỏi trắc nghiệm.');
                }
                if (!isset($data['correct_answer']) || !array_key_exists((int)$data['correct_answer'], $data['answers'])) {
                    throw new \Exception('Đáp án đúng không hợp lệ: ' . ($data['correct_answer'] ?? 'không xác định'));
                }
                foreach ($data['answers'] as $index => $answer) {
                    if (isset($answer['text']) && !empty(trim($answer['text']))) {
                        $isCorrect = ((int)$data['correct_answer'] === $index);
                        \Log::info('Tạo đáp án:', [
                            'index' => $index,
                            'text' => $answer['text'],
                            'is_correct' => $isCorrect,
                            'correct_answer' => $data['correct_answer'],
                        ]);
                        $this->answerRepo->create([
                            'question_id' => $question->id,
                            'answer'      => $answer['text'],
                            'is_correct'  => $isCorrect,
                        ]);
                    }
                }
            } elseif ($data['answer_type'] === 'text_input') {
                if (!isset($data['text_answer']) || empty(trim($data['text_answer']))) {
                    throw new \Exception('Đáp án văn bản không được để trống.');
                }
                $this->answerRepo->create([
                    'question_id' => $question->id,
                    'answer'      => $data['text_answer'],
                    'is_correct'  => true,
                ]);
            } elseif ($data['answer_type'] === 'true_false') {
                if (!isset($data['correct_answer']) || !in_array($data['correct_answer'], ['0', '1'])) {
                    throw new \Exception('Đáp án đúng cho câu hỏi Đúng/Sai không hợp lệ.');
                }
                $correct = (int)$data['correct_answer'] === 1;
                $this->answerRepo->createMany([
                    ['question_id' => $question->id, 'answer' => 'Đúng', 'is_correct' => $correct],
                    ['question_id' => $question->id, 'answer' => 'Sai', 'is_correct' => !$correct],
                ]);
            } else {
                throw new \Exception('Loại câu hỏi không hợp lệ.');
            }

            $question = $this->questionRepo->findById($question->id);
            \Log::info('Dữ liệu sau khi tạo:', $question->answers->toArray());
            return $question;
        });
    }

    public function updateWithAnswers(int $questionId, array $data): Question
    {
        return DB::transaction(function () use ($questionId, $data) {
            \Log::info('Dữ liệu đầu vào updateWithAnswers:', $data);

            $this->questionRepo->update($questionId, [
                'quiz_id'  => $data['quiz_id'],
                'question' => $data['question'],
                'order'    => $data['order'] ?? 0,
                'type'     => $data['answer_type'],
            ]);

            $this->answerRepo->deleteByQuestionId($questionId);

            if ($data['answer_type'] === 'multiple_choice') {
                if (!isset($data['answers']) || !is_array($data['answers']) || count($data['answers']) < 2) {
                    throw new \Exception('Cần ít nhất hai đáp án cho câu hỏi trắc nghiệm.');
                }
                if (!isset($data['correct_answer']) || !array_key_exists((int)$data['correct_answer'], $data['answers'])) {
                    throw new \Exception('Đáp án đúng không hợp lệ: ' . ($data['correct_answer'] ?? 'không xác định'));
                }
                foreach ($data['answers'] as $index => $answer) {
                    if (isset($answer['text']) && !empty(trim($answer['text']))) {
                        $isCorrect = ((int)$data['correct_answer'] === $index);
                        \Log::info('Tạo đáp án:', [
                            'index' => $index,
                            'text' => $answer['text'],
                            'is_correct' => $isCorrect,
                            'correct_answer' => $data['correct_answer'],
                        ]);
                        $this->answerRepo->create([
                            'question_id' => $questionId,
                            'answer'      => $answer['text'],
                            'is_correct'  => $isCorrect,
                        ]);
                    }
                }
            } elseif ($data['answer_type'] === 'text_input') {
                if (!isset($data['text_answer']) || empty(trim($data['text_answer']))) {
                    throw new \Exception('Đáp án văn bản không được để trống.');
                }
                $this->answerRepo->create([
                    'question_id' => $questionId,
                    'answer'      => $data['text_answer'],
                    'is_correct'  => true,
                ]);
            } elseif ($data['answer_type'] === 'true_false') {
                if (!isset($data['correct_answer']) || !in_array($data['correct_answer'], ['0', '1'])) {
                    throw new \Exception('Đáp án đúng cho câu hỏi Đúng/Sai không hợp lệ.');
                }
                $correct = (int)$data['correct_answer'] === 1;
                $this->answerRepo->createMany([
                    ['question_id' => $questionId, 'answer' => 'Đúng', 'is_correct' => $correct],
                    ['question_id' => $questionId, 'answer' => 'Sai', 'is_correct' => !$correct],
                ]);
            } else {
                throw new \Exception('Loại câu hỏi không hợp lệ.');
            }

            $question = $this->questionRepo->findById($questionId);
            \Log::info('Dữ liệu sau khi cập nhật:', $question->answers->toArray());
            return $question;
        });
    }

    public function paginateByQuizId(int $quizId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->questionRepo->paginateByQuizId($quizId, $perPage);
    }
}