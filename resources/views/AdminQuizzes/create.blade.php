@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Chỉnh sửa Câu Hỏi Trong Quiz: <strong>{{ $quiz->name }}</strong></h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.quizzes.questions.update', [$quiz->id, $question->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="question" class="form-label">Câu hỏi</label>
            <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror"
                value="{{ old('question', $question->question) }}" required>
            @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Có thể thêm chỉnh sửa câu trả lời ở đây -->

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
