<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuizRepository implements QuizRepositoryInterface
{
    public function create(array $data): Quiz
    {
        return Quiz::create($data);
    }
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Quiz::paginate($perPage);
    }       

    public function findById(int $id): ?Quiz
    {
        return Quiz::find($id);
    }

    public function all(): iterable
    {
        return Quiz::all();
    }

    public function update(int $id, array $data): bool
    {
        $quiz = $this->findById($id);
        if ($quiz) {
            return $quiz->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $quiz = $this->findById($id);
        if ($quiz) {
            return $quiz->delete();
        }
        return false;
    }

    public function search(string $keyword = null)
        {
         return Quiz::when($keyword, function ($query, $keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
}

}
