@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Thông báo lỗi -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Chỉnh sửa Quiz -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">Chỉnh sửa Quiz</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Tên quiz -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Tên Quiz</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $quiz->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mô tả quiz -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $quiz->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Giới hạn thời gian -->
                <div class="mb-3">
                    <label for="time_limit" class="form-label fw-bold">Giới hạn thời gian (phút)</label>
                    <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit', $quiz->time_limit ?? 10) }}" required min="1">
                    @error('time_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Công khai -->
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_public" name="is_public" value="1" {{ old('is_public', $quiz->is_public) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_public">Công khai</label>
                </div>

                <!-- Nút cập nhật -->
                <button type="submit" class="btn btn-primary px-4">Cập nhật Quiz</button>
            </form>

            <!-- Nút thêm câu hỏi -->
            <form action="{{ route('admin.quizzes.questions.create', $quiz->id) }}" method="GET" class="d-inline mt-3">
    @csrf
    <button type="submit" class="btn btn-secondary px-4">Thêm câu hỏi</button>
</form>
        </div>
    </div>

    <h4 class="mt-5 mb-3 fw-bold">Danh sách câu hỏi hiện có:</h4>
    @if (isset($questions) && $questions->isEmpty())
        <div class="alert alert-info">Chưa có câu hỏi nào được thêm.</div>
    @elseif (isset($questions))
        <ul class="list-group list-group-flush">
            @foreach ($questions as $question)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $question->question }}</span>
                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<!-- CSS tùy chỉnh -->
<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .form-control, .form-check-input {
        border-radius: 8px;
    }
    .btn {
        border-radius: 8px;
        transition: background-color 0.3s;
    }
    .list-group-item {
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .alert-dismissible {
        border-radius: 8px;
    }
</style>
@endsection
