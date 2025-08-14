<?php

namespace App\Services;

use App\Repositories\FlashcardRepositoryInterface;

class FlashcardService
{
    protected $flashcardRepository;

    public function __construct(FlashcardRepositoryInterface $flashcardRepository)
    {
        $this->flashcardRepository = $flashcardRepository;
    }

    public function getAll()
    {
        return $this->flashcardRepository->all();
    }

    public function getById($id)
    {
        return $this->flashcardRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->flashcardRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->flashcardRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->flashcardRepository->delete($id);
    }
}
