@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Tạo câu hỏi mới: {{ $quiz->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz->id) }}">
        @csrf

        <div class="mb-3">
            <label for="question" class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
            <textarea name="question" id="question" rows="3" class="form-control" required>{{ old('question') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
            <input type="number" name="order" id="order" class="form-control" value="{{ old('order', 1) }}" min="1" required>
        </div>

        <div class="mb-3">
            <label for="answer_type" class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
            <select name="answer_type" id="answer_type" class="form-select" required>
                <option value="multiple_choice" {{ old('answer_type') === 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                <option value="text_input" {{ old('answer_type') === 'text_input' ? 'selected' : '' }}>Nhập văn bản</option>
                <option value="true_false" {{ old('answer_type') === 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
            </select>
        </div>

        {{-- Multiple Choice --}}
        <div id="multiple-choice-section" style="display: none;">
            <h5>Đáp án (Multiple Choice)</h5>
            <div id="answers-wrapper"></div>
            <button type="button" class="btn btn-secondary mb-3" id="btn-add-answer">+ Thêm đáp án</button>
            <div class="mb-3">
                <label>Chọn đáp án đúng:</label>
            </div>
        </div>

        {{-- Text Input --}}
        <div id="text-input-section" style="display: none;">
            <div class="mb-3">
                <label for="text_answer" class="form-label">Đáp án chính xác <span class="text-danger">*</span></label>
                <input type="text" name="text_answer" id="text_answer" class="form-control" value="{{ old('text_answer') }}">
            </div>
        </div>

        {{-- True/False --}}
        <div id="true-false-section" style="display: none;">
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Chọn đáp án đúng <span class="text-danger">*</span></label>
                <select name="correct_answer" id="correct_answer" class="form-select" required>
                    <option value="1" {{ old('correct_answer') === '1' ? 'selected' : '' }}>Đúng</option>
                    <option value="0" {{ old('correct_answer') === '0' ? 'selected' : '' }}>Sai</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Lưu câu hỏi</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const answerTypeSelect = document.getElementById('answer_type');
    const multipleChoiceSection = document.getElementById('multiple-choice-section');
    const textInputSection = document.getElementById('text-input-section');
    const trueFalseSection = document.getElementById('true-false-section');
    const answersWrapper = document.getElementById('answers-wrapper');
    const addAnswerBtn = document.getElementById('btn-add-answer');

    function toggleSections() {
        const type = answerTypeSelect.value;
        multipleChoiceSection.style.display = type === 'multiple_choice' ? 'block' : 'none';
        textInputSection.style.display = type === 'text_input' ? 'block' : 'none';
        trueFalseSection.style.display = type === 'true_false' ? 'block' : 'none';
    }

    function addAnswer(text = '') {
        const answerDiv = document.createElement('div');
        answerDiv.classList.add('input-group', 'mb-2');
        answerDiv.innerHTML = `
            <div class="input-group-text">
                <input type="radio" name="correct_answer" required>
            </div>
            <input type="text" class="form-control" placeholder="Nội dung đáp án" value="${text}" required>
            <button type="button" class="btn btn-danger btn-remove-answer">&times;</button>
        `;
        answersWrapper.appendChild(answerDiv);

        answerDiv.querySelector('.btn-remove-answer').addEventListener('click', () => {
            answerDiv.remove();
            reIndexAnswers();
        });

        reIndexAnswers();
    }

    function reIndexAnswers() {
        const answerDivs = answersWrapper.querySelectorAll('.input-group');
        answerDivs.forEach((div, idx) => {
            const radio = div.querySelector('input[type=radio]');
            const textInput = div.querySelector('input[type=text]');
            radio.value = idx;
            radio.name = "correct_answer";
            textInput.name = `answers[${idx}][text]`;
            textInput.placeholder = `Nội dung đáp án ${idx + 1}`;
        });
    }

    addAnswerBtn.addEventListener('click', () => {
        addAnswer();
    });

    answerTypeSelect.addEventListener('change', function() {
        toggleSections();
        if (answerTypeSelect.value === 'multiple_choice' && answersWrapper.childElementCount === 0) {
            addAnswer();
            addAnswer();
        }
    });

    // Khởi tạo mặc định
    toggleSections();
    if (answerTypeSelect.value === 'multiple_choice') {
        addAnswer();
        addAnswer();
    }
});
</script>
@endsection
