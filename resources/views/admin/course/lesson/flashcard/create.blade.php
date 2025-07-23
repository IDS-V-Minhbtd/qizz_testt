@extends('adminlte::page')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Thêm Flashcard mới</h2>
    <form action="{{ route('admin.lessons.flashcards.store', $lessonId) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="front" class="form-label">Mặt trước</label>
            <input type="text" name="front" id="front" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="back" class="form-label">Mặt sau</label>
            <input type="text" name="back" id="back" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success rounded-pill px-4">Lưu</button>
        <a href="{{ route('admin.lessons.flashcards.index', $lessonId) }}" class="btn btn-secondary rounded-pill px-4 ms-2">Hủy</a>
    </form>
</div>
@endsection
