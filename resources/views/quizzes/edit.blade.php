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
        </div>
    </div>

    <!-- Thêm câu hỏi -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h2 class="h4 mb-0">Thêm câu hỏi cho Quiz</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.quizzes.questions.store', $quiz->id ?? '') }}" method="POST">
                @csrf

                <!-- Câu hỏi -->
                <div class="mb-3">
                    <label for="question" class="form-label fw-bold">Câu hỏi</label>
                    <input type="text" class="form-control @error('question') is-invalid @enderror" name="question" value="{{ old('question') }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thứ tự câu hỏi -->
                <div class="mb-3">
                    <label for="order" class="form-label fw-bold">Thứ tự câu hỏi</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" name="order" value="{{ old('order', 1) }}" required min="1">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Loại câu trả lời -->
                <div class="mb-3">
                    <label for="answer_type" class="form-label fw-bold">Loại câu trả lời</label>
                    <select class="form-select @error('answer_type') is-invalid @enderror" id="answer_type" name="answer_type" required>
                        <option value="multiple_choice" {{ old('answer_type', $quiz->answer_type) == 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                        <option value="text_input" {{ old('answer_type', $quiz->answer_type) == 'text_input' ? 'selected' : '' }}>Nhập văn bản</option>
                    </select>
                    @error('answer_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nút thêm câu hỏi -->
                <button type="submit" class="btn btn-success px-4">Thêm câu hỏi</button>
            </form>

            <!-- Danh sách câu hỏi -->
            <h4 class="mt-5 mb-3 fw-bold">Danh sách câu hỏi hiện có:</h4>
            @if ($questions->isEmpty())
                <div class="alert alert-info">Chưa có câu hỏi nào được thêm.</div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($questions as $question)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $question->question }}</span>
                            <div>
                                <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="btn btn-sm btn-warning me-2">Sửa</a>
                                <form action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">Xóa</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
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