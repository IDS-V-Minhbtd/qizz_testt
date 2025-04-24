<?php

namespace App\Services;

use App\Repositories\Interfaces\AnswerRepositoryInterface;

class AnswerService
{
    public function __construct(protected AnswerRepositoryInterface $answerRepo) {}

    public function create(array $data)
    {
        return $this->answerRepo->create($data);
    }

    public function update($id, array $data)
    {
        $answer = $this->answerRepo->find($id);
        return $this->answerRepo->update($answer, $data);
    }

    public function delete($id)
    {
        $answer = $this->answerRepo->find($id);
        return $this->answerRepo->delete($answer);
    }

    public function getById($id)
    {
        return $this->answerRepo->find($id);
    }
}