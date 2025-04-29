<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;

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
            // Tạo câu hỏi
            $question = $this->questionRepo->create([
                'quiz_id'  => $data['quiz_id'],
                'question' => $data['question'],
                'order'    => $data['order'] ?? 0,
                'type'     => $data['answer_type'],
            ]);

            // Debugging: Kiểm tra cấu trúc của $data['answers']
            if (is_array($data['answers'])) {
                // Đảm bảo mỗi đáp án có key 'text'
                foreach ($data['answers'] as $index => $answer) {
                    if (isset($answer['text'])) {
                        $this->answerRepo->create([
                            'question_id' => $question->id,
                            'answer'      => $answer['text'],
                            'is_correct'  => ((int)$data['correct_answer'] === $index),
                        ]);
                    } else {
                        // Ghi lỗi hoặc ném ngoại lệ nếu 'text' thiếu
                        throw new \Exception("Missing 'text' in answer at index {$index}");
                    }
                }
            } else {
                // Ghi lỗi hoặc ném ngoại lệ nếu answers không phải là mảng
                throw new \Exception('Answers must be an array.');
            }

            // Xử lý các loại câu trả lời khác...
            if ($data['answer_type'] === 'text_input') {
                // Tạo câu trả lời văn bản (text_input)
                $this->answerRepo->create([
                    'question_id' => $question->id,
                    'answer'      => $data['text_answer'],
                    'is_correct'  => true, // Chỉ có một câu trả lời đúng duy nhất
                ]);
            } elseif ($data['answer_type'] === 'true_false') {
                $correct = (int)$data['correct_answer'] === 1;
                $this->answerRepo->createMany([
                    ['question_id' => $question->id, 'answer' => 'Đúng', 'is_correct' => $correct],
                    ['question_id' => $question->id, 'answer' => 'Sai',  'is_correct' => !$correct],
                ]);
            }

            return $question;
        });
    }

    public function createTextInput(array $data, string $textAnswer): Question
    {
        return DB::transaction(function () use ($data, $textAnswer) {
            $question = $this->questionRepo->create([
                'quiz_id'  => $data['quiz_id'],
                'question' => $data['question'],
                'order'    => $data['order'] ?? 0,
                'type'     => $data['answer_type'],
            ]);

            $this->answerRepo->create([
                'question_id' => $question->id,
                'answer'      => $textAnswer,
                'is_correct'  => true,
            ]);

            return $question;
        });
    }
}
