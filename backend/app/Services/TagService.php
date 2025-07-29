<?php

namespace App\Services;

use App\Repositories\Interfaces\TagRepositoryInterface;

class TagService
{
    protected TagRepositoryInterface $tagRepo;

    public function __construct(TagRepositoryInterface $tagRepo)
    {
        $this->tagRepo = $tagRepo;
    }

    public function getAll()
    {
        return $this->tagRepo->all();
    }

    public function getById($id)
    {
        return $this->tagRepo->findById($id);
    }

    public function create(array $data)
    {
        return $this->tagRepo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->tagRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->tagRepo->delete($id);
    }
}
