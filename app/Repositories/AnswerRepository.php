<?php
namespace App\Repositories;

use App\Models\Answer;
use App\Repositories\Interfaces\AnswerRepositoryInterface;

class AnswerRepository implements AnswerRepositoryInterface
{
    public function create(array $data): Answer
    {
        return Answer::create($data);
    }

    public function findById(int $id): ?Answer
    {
        return Answer::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $answer = $this->findById($id);
        if ($answer) {
            return $answer->update($data);
        }
        return false;
    }

    public function all(): iterable
    {
        return Answer::all();
    }
    public function getByQuestionId($questionId)
{
    return Answer::where('question_id', $questionId)->get();
}
    
    public function deleteByQuestionId(int $questionId): bool
    {
        return Answer::where('question_id', $questionId)->delete();
    }

}
