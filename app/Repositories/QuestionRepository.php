<?php
namespace App\Repositories;

use App\Models\Question;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionRepository implements QuestionRepositoryInterface
{
    // Create a new question
    public function create(array $data): Question
    {
        return Question::create($data);
    }

    // Find a question by ID
    public function findById(int $id): ?Question
    {
        return Question::find($id);
    }

    // Update an existing question
    public function update(int $id, array $data): bool
    {
        $question = $this->findById($id);
        if ($question) {
            return $question->update($data);
        }
        return false;
    }

    // Delete a question by ID
    public function delete(int $id): bool
    {
        $question = $this->findById($id);
        if ($question) {
            return $question->delete();
        }
        return false;
    }

    // Get all questions
    public function all(): iterable
    {
        return Question::all();
    }

    // Get questions by quiz ID, ordered by their 'order' field
    public function findByQuizId(int $quizId): iterable
    {
        return Question::where('quiz_id', $quizId)->orderBy('order')->get();
    }

    // Find a specific question by quiz ID and question ID
    public function findByQuizIdAndQuestionId(int $quizId, int $questionId): ?Question
    {
        return Question::where('quiz_id', $quizId)
                       ->where('id', $questionId)
                       ->first();
    }
    public function paginateByQuizId(int $quizId, int $perPage = 10): LengthAwarePaginator
{
    return Question::where('quiz_id', $quizId)->orderBy('order')->paginate($perPage);
}
    // Get the count of questions by quiz ID
    public function countByQuizId(int $quizId): int
    {
        return Question::where('quiz_id', $quizId)->count();
    }

}
