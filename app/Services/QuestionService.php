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
            $question = $this->questionRepo->create([
                'quiz_id'  => $data['quiz_id'],
                'question' => $data['question'],
                'order'    => $data['order'] ?? 0,
                'type'     => $data['answer_type'],
            ]);

            if ($data['answer_type'] === 'multiple_choice') {
                foreach ($data['answers'] as $index => $answer) {
                    if (isset($answer['text'])) {
                        $this->answerRepo->create([
                            'question_id' => $question->id,
                            'answer'      => $answer['text'],
                            'is_correct'  => ((int)$data['correct_answer'] === $index),
                        ]);
                    }
                }
            } elseif ($data['answer_type'] === 'text_input') {
                $this->answerRepo->create([
                    'question_id' => $question->id,
                    'answer'      => $data['text_answer'],
                    'is_correct'  => true,
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

    public function updateWithAnswers(int $questionId, array $data): Question
{
    return DB::transaction(function () use ($questionId, $data) {
        // Cập nhật câu hỏi
        $question = $this->questionRepo->update($questionId, [
            'quiz_id'  => $data['quiz_id'],
            'question' => $data['question'],
            'order'    => $data['order'] ?? 0,
            'type'     => $data['answer_type'], // Đảm bảo rằng bạn gán đúng vào trường 'type'
        ]);

        // Xóa câu trả lời cũ trước khi thêm câu trả lời mới
        $this->answerRepo->deleteByQuestionId($questionId);

        // Thêm câu trả lời mới theo loại câu hỏi
        if ($data['answer_type'] === 'multiple_choice') {
            foreach ($data['answers'] as $index => $answer) {
                if (isset($answer['text'])) {
                    $this->answerRepo->create([
                        'question_id' => $questionId,
                        'answer'      => $answer['text'],
                        'is_correct'  => ((int)$data['correct_answer'] === $index),
                    ]);
                }
            }
        } elseif ($data['answer_type'] === 'text_input') {
            $this->answerRepo->create([
                'question_id' => $questionId,
                'answer'      => $data['text_answer'],
                'is_correct'  => true, // Nếu cần, bạn có thể thay đổi logic này để phù hợp
            ]);
        } elseif ($data['answer_type'] === 'true_false') {
            $correct = (int)$data['correct_answer'] === 1;
            $this->answerRepo->createMany([
                ['question_id' => $questionId, 'answer' => 'Đúng', 'is_correct' => $correct],
                ['question_id' => $questionId, 'answer' => 'Sai',  'is_correct' => !$correct],
            ]);
        }

        // Trả về câu hỏi đã được cập nhật
        return $this->questionRepo->findById($questionId);
    });
}

}
