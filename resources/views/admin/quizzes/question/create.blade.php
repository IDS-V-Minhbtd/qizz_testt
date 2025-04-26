@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Thêm Câu Hỏi Mới vào Quiz: <strong>{{ $quiz->name }}</strong></h2>

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

        <div class="mb-3">
            <label for="question" class="form-label">Câu hỏi</label>
            <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror"
                value="{{ old('question') }}" required>
            @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự</label>
            <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror"
                value="{{ old('order') }}" required min="1">
            @error('order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="answer_type" class="form-label">Loại câu trả lời</label>
            <select name="answer_type" id="answer_type" class="form-select @error('answer_type') is-invalid @enderror" required>
                <option value="multiple_choice" {{ old('answer_type') == 'multiple_choice' ? 'selected' : '' }}>Lựa chọn nhiều</option>
                <option value="text_input" {{ old('answer_type') == 'text_input' ? 'selected' : '' }}>Nhập văn bản</option>
                <option value="true_false" {{ old('answer_type') == 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
            </select>
            @error('answer_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div id="multiple-choice-answers" style="display: none;">
            <div class="mb-3">
                <label for="answers" class="form-label">Các đáp án</label>
                <div id="answers-container">
                    <div class="input-group mb-3">
                        <input type="text" name="answers[]" class="form-control" placeholder="Đáp án 1" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="answers[]" class="form-control" placeholder="Đáp án 2" required>
                    </div>
                    <!-- Add more answers as needed -->
                </div>
                <button type="button" class="btn btn-secondary" id="add-answer">Thêm đáp án</button>
            </div>
        </div>

        <div id="text-input-answer" style="display: none;">
            <div class="mb-3">
                <label for="text_answer" class="form-label">Đáp án văn bản</label>
                <input type="text" name="text_answer" id="text_answer" class="form-control" value="{{ old('text_answer') }}" required>
            </div>
        </div>

        <div id="true-false-answers" style="display: none;">
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Chọn đáp án đúng</label>
                <select name="correct_answer" id="correct_answer" class="form-select" required>
                    <option value="1" {{ old('correct_answer') == 1 ? 'selected' : '' }}>Đúng</option>
                    <option value="0" {{ old('correct_answer') == 0 ? 'selected' : '' }}>Sai</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Thêm Câu Hỏi</button>
        <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('answer_type').addEventListener('change', function () {
        const type = this.value;

        document.getElementById('multiple-choice-answers').style.display = (type === 'multiple_choice') ? 'block' : 'none';
        document.getElementById('text-input-answer').style.display = (type === 'text_input') ? 'block' : 'none';
        document.getElementById('true-false-answers').style.display = (type === 'true_false') ? 'block' : 'none';
    });

    document.getElementById('add-answer').addEventListener('click', function () {
        const container = document.getElementById('answers-container');
        const newAnswerInput = document.createElement('div');
        newAnswerInput.classList.add('input-group', 'mb-3');
        newAnswerInput.innerHTML = '<input type="text" name="answers[]" class="form-control" placeholder="Đáp án mới" required>';
        container.appendChild(newAnswerInput);
    });

    // Lắng nghe sự kiện khi trang load để hiển thị đúng phần tử
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('answer_type').dispatchEvent(new Event('change'));
    });
</script>
@endsection
