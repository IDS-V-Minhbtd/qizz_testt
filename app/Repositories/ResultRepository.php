<?php
namespace App\Repositories;

use App\Models\Result;
use App\Repositories\Interfaces\ResultRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ResultRepository implements ResultRepositoryInterface
{
    protected Model $model;

    public function __construct(Result $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Result
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Result
    {
        return $this->model->find($id);
    }

    public function delete(int $id): bool
    {
        $result = $this->findById($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function findByIdWithAnswers(int $id)
    {
        // Lấy kết quả cùng các câu trả lời của người dùng và các câu hỏi tương ứng
        return Result::with(['userAnswers.question', 'userAnswers.answer'])  // Thêm answer nếu cần
            ->find($id);
    }
    public function getByUser(int $userId): iterable
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
