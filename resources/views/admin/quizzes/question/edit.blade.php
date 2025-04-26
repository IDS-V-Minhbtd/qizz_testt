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

    <!-- Form cập nhật câu hỏi -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">Chỉnh sửa Câu Hỏi</h1>
        </div>
        <div class="card-body">
            <!-- Form Update Question -->
            <form action="{{ route('admin.quizzes.questions.update', [$quizId, $question->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Question -->
                <div class="mb-3">
                    <label for="question" class="form-label fw-bold">Question</label>
                    <input type="text" class="form-control @error('question') is-invalid @enderror" id="question" name="question" value="{{ old('question', $question->question) }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Order -->
                <div class="mb-3">
                    <label for="order" class="form-label fw-bold">Order</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $question->order) }}" min="1" required>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Answer Type -->
                <div class="mb-3">
                    <label for="answer_type" class="form-label fw-bold">Answer Type</label>
                    <select class="form-select @error('answer_type') is-invalid @enderror" id="answer_type" name="answer_type" required>
                        <option value="multiple_choice" {{ old('answer_type', $question->answer_type) == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="text_input" {{ old('answer_type', $question->answer_type) == 'text_input' ? 'selected' : '' }}>Text Input</option>
                    </select>
                    @error('answer_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nút cập nhật câu hỏi -->
                <button type="submit" class="btn btn-primary">Cập nhật Câu Hỏi</button>
            </form>
        </div>
    </div>
</div>
@endsection
