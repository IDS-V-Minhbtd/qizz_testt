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
        if (empty($data['code'])) {
            $data['code'] = $this->generateUniqueCode();
        }
        return $this->quizRepo->create($data);
    }

    // Sinh code ngẫu nhiên, không trùng
    protected function generateUniqueCode($length = 8)
    {
        do {
            $code = strtoupper(\Str::random($length));
        } while ($this->quizRepo->all()->contains('code', $code));
        return $code;
    }

    public function getById(int $id): ?Quiz
    {
        return $this->quizRepo->findById($id);
    }

    public function getAll(): iterable
    {
        return $this->quizRepo->all();
    }
    public function update(int $id, array $data): bool
    {
        return $this->quizRepo->update($id, $data);
    }
    public function delete(int $id): bool
    {
        return $this->quizRepo->delete($id);
    }
    public function search(string $keyword = null)
    {
        return $this->quizRepo->search($keyword);
    }


  
}
