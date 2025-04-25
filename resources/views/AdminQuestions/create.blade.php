@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Thêm Câu Hỏi Mới Cho Quiz: <strong>{{ $quiz->name }}</strong></h2>

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

    <form action="{{ route('admin.quizzes.questions.store', $quiz->id) }}" method="POST">
        @csrf

        <!-- Nội dung câu hỏi -->
        <div class="mb-3">
            <label for="question" class="form-label">Câu hỏi</label>
            <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question') }}" required>
            @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- 4 đáp án -->
        @for ($i = 1; $i <= 4; $i++)
            <div class="mb-3">
                <label for="answers[{{ $i }}]" class="form-label">Đáp án {{ $i }}</label>
                <div class="input-group">
                    <input type="text" name="answers[{{ $i }}][text]" class="form-control @error('answers.'.$i.'.text') is-invalid @enderror" value="{{ old("answers.$i.text") }}" required>
                    <div class="input-group-text">
                        <input type="radio" name="correct_answer" value="{{ $i }}" {{ old('correct_answer') == $i ? 'checked' : '' }} required>
                        <span class="ms-1">Đúng</span>
                    </div>
                </div>
                @error('answers.'.$i.'.text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endfor

        <!-- Submit -->
        <button type="submit" class="btn btn-success">Thêm Câu Hỏi</button>
        <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-secondary">Quay lại</a>
    </form>

    @if (isset($questions) && !$questions->isEmpty())
        <hr>
        <h5 class="mt-4">Danh sách câu hỏi đã có:</h5>
        <ul class="list-group">
            @foreach ($questions as $q)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $q->question }}
                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $q->id]) }}" class="btn btn-sm btn-warning">Chỉnh sửa</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
