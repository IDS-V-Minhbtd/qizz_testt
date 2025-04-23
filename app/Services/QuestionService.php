<?php
namespace App\Services;

use App\Repositories\Interfaces\QuestionRepositoryInterface;

class QuestionService
{
    public function __construct(protected QuestionRepositoryInterface $questionRepo) {}

    public function create(array $data): Question
    {
        return $this->questionRepo->create($data);
    }

    public function getById(int $id): ?Question
    {
        return $this->questionRepo->findById($id);
    }
    public function update(int $id, array $data): bool
    {
        return $this->questionRepo->update($id, $data);
    }
    public function delete(int $id): bool
    {
        return $this->questionRepo->delete($id);
    }
    public function getAll(): iterable
    {
        return $this->questionRepo->all();
    }
    public function getByQuizId(int $quizId): iterable
    {
        return $this->questionRepo->findByQuizId($quizId);
    }
    public function getByQuizIdAndQuestionId(int $quizId, int $questionId): ?Question
    {
        return $this->questionRepo->findByQuizIdAndQuestionId($quizId, $questionId);
    }
}