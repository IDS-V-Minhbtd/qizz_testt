<?php

namespace App\Services;

use App\Repositories\Interfaces\CatalogRepositoryInterface;

class CatalogService
{
    protected CatalogRepositoryInterface $catalogRepo;

    public function __construct(CatalogRepositoryInterface $catalogRepo)
    {
        $this->catalogRepo = $catalogRepo;
    }

    public function getAll()
    {
        return $this->catalogRepo->all();
    }

    public function getById($id)
    {
        return $this->catalogRepo->findById($id);
    }

    public function create(array $data)
    {
        return $this->catalogRepo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->catalogRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->catalogRepo->delete($id);
    }
}
