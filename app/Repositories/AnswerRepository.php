<?php 
namespace App\Repositories;

use App\Models\Answer;

class AnswerRepository
{
    protected $model;

    public function __construct(Answer $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Answer $answer, array $data)
    {
        return $answer->update($data);
    }

    public function delete(Answer $answer)
    {
        return $answer->delete();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
}