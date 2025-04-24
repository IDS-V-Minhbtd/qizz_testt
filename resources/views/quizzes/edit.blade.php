@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Chỉnh sửa Quiz</h1>
    <form action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Tên quiz -->
        <div class="mb-3">
            <label for="name" class="form-label">Tên Quiz</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $quiz->name) }}" required>
        </div>
        
        <!-- Mô tả quiz -->
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $quiz->description) }}</textarea>
        </div>
        
        <!-- Nút cập nhật -->
        <button type="submit" class="btn btn-primary">Cập nhật Quiz</button>
    </form>
</div>

<div class="container mt-5">
    <h2 class="mb-4">Thêm câu hỏi cho Quiz</h2>
    
    <form action="{{ route('admin.quizzes.questions.store', $quiz->id ?? '') }}" method="POST">
        @csrf
        
        <!-- Câu hỏi -->
        <div class="mb-3">
            <label for="question" class="form-label">Câu hỏi</label>
            <input type="text" class="form-control" name="question" value="{{ old('question') }}" required>
        </div>

        <!-- Thứ tự câu hỏi -->
        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự câu hỏi</label>
            <input type="number" class="form-control" name="order" value="1" required min="1">
        </div>

        <h4 class="mb-3">Danh sách câu hỏi hiện có:</h4>
        <ul class="list-group">
            @foreach ($questions as $question)
                <li class="list-group-item">
                    {{ $question->question }}
                </li>
            @endforeach
        </ul>

        <!-- Nút thêm câu hỏi -->
        <button type="submit" class="btn btn-success mt-3">Thêm câu hỏi</button>
    </form>
</div>
@endsection
