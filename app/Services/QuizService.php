<?php
namespace App\Services;

use App\Models\Quiz;
use App\Repositories\Interfaces\QuizRepositoryInterface;

class QuizService
{
    public function __construct(protected QuizRepositoryInterface $quizRepo) {}

    public function create(array $data): Quiz
    {
        $data['created_by'] = auth()->id();
        return $this->quizRepo->create($data);
    }

    public function getById(int $id): ?Quiz
    {
        return $this->quizRepo->findById($id);
    }

    public function getAll(): iterable
    {
        return $this->quizRepo->all();
    }

  
}
