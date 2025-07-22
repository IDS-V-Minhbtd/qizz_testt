<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FlashcardService;

class FlashcardController extends Controller
{
    protected $flashcardService;

    public function __construct(FlashcardService $flashcardService)
    {
        $this->flashcardService = $flashcardService;
    }

    public function index()
    {
        $flashcards = $this->flashcardService->getAll();
        return view('flashcards.index', compact('flashcards'));
    }

    public function create()
    {
        return view('flashcards.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'front' => 'required|string',
            'back' => 'required|string',
        ]);
        $this->flashcardService->create($data);
        return redirect()->route('flashcards.index');
    }

    public function edit($id)
    {
        $flashcard = $this->flashcardService->getById($id);
        return view('flashcards.edit', compact('flashcard'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'front' => 'required|string',
            'back' => 'required|string',
        ]);
        $this->flashcardService->update($id, $data);
        return redirect()->route('flashcards.index');
    }

    public function destroy($id)
    {
        $this->flashcardService->delete($id);
        return redirect()->route('flashcards.index');
    }
}
