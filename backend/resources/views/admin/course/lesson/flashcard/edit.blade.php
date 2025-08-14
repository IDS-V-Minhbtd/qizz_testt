@extends('adminlte::page')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Chỉnh sửa Flashcard</h2>
    <form action="{{ route('admin.lessons.flashcards.update', [$lessonId, $flashcard->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="front" class="form-label">Mặt trước</label>
            <input type="text" name="front" id="front" class="form-control" value="{{ $flashcard->front }}" required>
        </div>
        <div class="mb-3">
            <label for="back" class="form-label">Mặt sau</label>
            <input type="text" name="back" id="back" class="form-control" value="{{ $flashcard->back }}" required>
        </div>
        <button type="submit" class="btn btn-success rounded-pill px-4">Cập nhật</button>
        <a href="{{ route('admin.lessons.flashcards.index', $lessonId) }}" class="btn btn-secondary rounded-pill px-4 ms-2">Hủy</a>
    </form>
</div>
@endsection
