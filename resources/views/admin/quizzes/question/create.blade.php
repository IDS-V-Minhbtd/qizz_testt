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
        <input type="hidden" name="answer_type" value="multiple_choice">

        <div class="mb-3">
            <label for="question" class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
            <textarea name="question" id="question" rows="3" class="form-control" >{{ old('question') }}</textarea>
            @error('question')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
            <input type="number" name="order" id="order" class="form-control" value="{{ old('order', 1) }}" min="1" >
            @error('order')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <h5>Đáp án (Trắc nghiệm)</h5>
            <div id="answers-wrapper">
                @php
                    $oldAnswers = old('answers', [1 => ['text' => ''], 2 => ['text' => '']]);
                    $maxId = max(array_keys($oldAnswers));
                @endphp
                @foreach ($oldAnswers as $id => $answer)
                    <div class="input-group mb-2" data-answer-id="{{ $id }}">
                        <span class="input-group-text">Đáp án {{ $id }}</span>
                        <input type="text" name="answers[{{ $id }}][text]" class="form-control answer-input" placeholder="Nội dung đáp án" value="{{ $answer['text'] }}" >
                        @if ($id > 2)
                            <button type="button" class="btn btn-danger btn-remove-answer">×</button>
                        @endif
                    </div>
                    @error("answers.{$id}.text")
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="btn-add-answer">+ Thêm đáp án</button>
            @error('answers')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="correct_answer" class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
            <select name="correct_answer" id="correct_answer" class="form-select" >
                @foreach ($oldAnswers as $id => $answer)
                    <option value="{{ $id }}" {{ old('correct_answer', 1) == $id ? 'selected' : '' }}>
                        Đáp án {{ $id }}: {{ $answer['text'] }}
                    </option>
                @endforeach
            </select>
            @error('correct_answer')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Lưu câu hỏi</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const answersWrapper = document.getElementById('answers-wrapper');
    const addAnswerBtn = document.getElementById('btn-add-answer');
    const correctAnswerSelect = document.getElementById('correct_answer');

    // Tính ID lớn nhất hiện tại
    let answerCounter = Math.max(...Array.from(answersWrapper.children).map(div => parseInt(div.dataset.answerId) || 0), 0);

    function updateCorrectAnswerDropdown() {
        correctAnswerSelect.innerHTML = '';
        Array.from(answersWrapper.children).forEach(div => {
            const id = div.dataset.answerId;
            const input = div.querySelector('input');
            const text = input.value || `Đáp án ${id}`;
            const option = document.createElement('option');
            option.value = id;
            option.textContent = `Đáp án ${id}: ${text}`;
            if (parseInt(correctAnswerSelect.value) === parseInt(id)) {
                option.selected = true;
            }
            correctAnswerSelect.appendChild(option);
        });
    }

    function addAnswer(text = '') {
        if (answersWrapper.children.length >= 10) {
            alert('Không thể thêm quá 10 đáp án.');
            return;
        }

        answerCounter++;
        const answerDiv = document.createElement('div');
        answerDiv.classList.add('input-group', 'mb-2');
        answerDiv.dataset.answerId = answerCounter;
        answerDiv.innerHTML = `
            <span class="input-group-text">Đáp án ${answerCounter}</span>
            <input type="text" name="answers[${answerCounter}][text]" class="form-control answer-input" placeholder="Nội dung đáp án" value="${text}" >
            <button type="button" class="btn btn-danger btn-remove-answer">×</button>
        `;
        answersWrapper.appendChild(answerDiv);

        answerDiv.querySelector('.btn-remove-answer').addEventListener('click', () => {
            if (confirm('Bạn có chắc chắn muốn xóa đáp án này?')) {
                answerDiv.remove();
                updateCorrectAnswerDropdown();
            }
        });

        // Cập nhật dropdown khi text thay đổi
        answerDiv.querySelector('input').addEventListener('input', updateCorrectAnswerDropdown);

        updateCorrectAnswerDropdown();
    }

    addAnswerBtn.addEventListener('click', () => {
        addAnswer();
    });

    // Gán sự kiện cho các đáp án hiện tại
    answersWrapper.querySelectorAll('.btn-remove-answer').forEach(btn => {
        btn.addEventListener('click', function () {
            if (confirm('Bạn có chắc chắn muốn xóa đáp án này?')) {
                this.closest('.input-group').remove();
                updateCorrectAnswerDropdown();
            }
        });
    }); 

    answersWrapper.querySelectorAll('.answer-input').forEach(input => {
        input.addEventListener('input', updateCorrectAnswerDropdown);
    });

    updateCorrectAnswerDropdown();
});
</script>
@endsection
