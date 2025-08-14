<?php

namespace App\Repositories;

use App\Models\Flashcard;

class FlashcardRepository implements FlashcardRepositoryInterface
{
    public function all()
    {
        return Flashcard::all();
    }

    public function find($id)
    {
        return Flashcard::findOrFail($id);
    }

    public function create(array $data)
    {
        return Flashcard::create($data);
    }

    public function update($id, array $data)
    {
        $flashcard = Flashcard::findOrFail($id);
        $flashcard->update($data);
        return $flashcard;
    }

    public function delete($id)
    {
        Flashcard::destroy($id);
    }
}
