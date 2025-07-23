<?php

namespace App\Repositories\Interfaces;

use App\Models\Catalog;

interface CatalogRepositoryInterface
{
    public function all();
    public function findById($id): ?Catalog;
    public function create(array $data): Catalog;
    public function update($id, array $data): bool;
    public function delete($id): bool;
}
