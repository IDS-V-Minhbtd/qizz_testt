<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;

class TagRepository implements TagRepositoryInterface
{
    public function all()
    {
        return Tag::all();
    }

    public function findById($id): ?Tag
    {
        return Tag::find($id);
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update($id, array $data): bool
    {
        $tag = $this->findById($id);
        return $tag ? $tag->update($data) : false;
    }

    public function delete($id): bool
    {
        $tag = $this->findById($id);
        return $tag ? $tag->delete() : false;
    }
}
