<?php
namespace App\Services;

use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;

class QuestionService
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo // Add QuizRepositoryInterface
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
}