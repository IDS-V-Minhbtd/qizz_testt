<?php

namespace App\Repositories;

use App\Models\Question;
use App\Models\Quiz;
use App\Repositories\Interfaces\QuestionRepositoryInterface;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function create(array $data): Question
    {
        return Question::create($data);
    }

    public function findById(int $id): ?Question
    {
        return Question::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $question = $this->findById($id);
        if ($question) {
            return $question->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $question = $this->findById($id);
        if ($question) {
            return $question->delete();
        }
        return false;
    }

    public function all(): iterable
    {
        return Question::all();
    }

    public function findByQuizId(int $quizId): iterable
    {
        return Question::where('quiz_id', $quizId)->orderBy('order')->get();
    }

    public function findByQuizIdAndQuestionId(int $quizId, int $questionId): ?Question
    {
        return Question::where('quiz_id', $quizId)
                       ->where('id', $questionId)
                       ->first();
    }
}
