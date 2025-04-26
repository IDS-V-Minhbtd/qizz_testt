<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;

class AnswerService
{
    protected $answerRepository;
    protected $questionRepository;

    public function __construct(
        AnswerRepositoryInterface $answerRepository,
        QuestionRepositoryInterface $questionRepository
    ) {
        $this->answerRepository = $answerRepository;
        $this->questionRepository = $questionRepository;
    }

    public function getByQuestionId($questionId)
    {
        return $this->answerRepository->findByQuestionId($questionId);
    }

    public function create(array $data)
    {
        return $this->answerRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->answerRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->answerRepository->delete($id);
    }

    public function createWithAnswers(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            $question = $this->questionRepository->create([
                'quiz_id'  => $data['quiz_id'],
                'question' => $data['question'],
                'order'    => $data['order'] ?? 0,
                'type'     => $data['answer_type'],
            ]);

            if ($data['answer_type'] === 'multiple_choice') {
                foreach ($data['answers'] as $index => $answer) {
                    $this->answerRepository->create([
                        'question_id' => $question->id,
                        'answer'      => $answer['text'],
                        'is_correct'  => ((int)$data['correct_answer'] === $index),
                    ]);
                }
            } elseif ($data['answer_type'] === 'text_input') {
                $this->answerRepository->create([
                    'question_id' => $question->id,
                    'answer'      => $data['text_answer'],
                    'is_correct'  => true,
                ]);
            } elseif ($data['answer_type'] === 'true_false') {
                $correct = (int)$data['correct_answer'] === 1;
                $this->answerRepository->createMany([
                    ['question_id' => $question->id, 'answer' => 'Đúng', 'is_correct' => $correct],
                    ['question_id' => $question->id, 'answer' => 'Sai',  'is_correct' => !$correct],
                ]);
            }

            return $question;
        });
    }
}
