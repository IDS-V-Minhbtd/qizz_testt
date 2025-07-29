<?php

namespace App\Repositories;

use App\Models\Catalog;
use App\Repositories\Interfaces\CatalogRepositoryInterface;

class CatalogRepository implements CatalogRepositoryInterface
{
    public function all()
    {
        return Catalog::all();
    }

    public function findById($id): ?Catalog
    {
        return Catalog::find($id);
    }

    public function create(array $data): Catalog
    {
        return Catalog::create($data);
    }

    public function update($id, array $data): bool
    {
        $catalog = $this->findById($id);
        return $catalog ? $catalog->update($data) : false;
    }

    public function delete($id): bool
    {
        $catalog = $this->findById($id);
        return $catalog ? $catalog->delete() : false;
    }
}
