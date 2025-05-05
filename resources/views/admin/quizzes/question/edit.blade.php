@php
    $answers = collect($answers ?? []);
    $correctAnswer = old('correct_answer');

    if ($correctAnswer === null) {
        if ($answers->isNotEmpty()) {
            $correctAnswer = $answers->search(function ($a) {
                return ($a['is_correct'] ?? $a->is_correct ?? false);
            });
        } elseif (isset($question) && $question->type === 'true_false') {
            $correctAnswer = $answers[0]['is_correct'] ?? $answers[0]->is_correct ?? null;
        } elseif (isset($question) && $question->type === 'text_input') {
            $correctAnswer = $answers[0]['answer'] ?? $answers[0]->answer ?? '';
        } else {
            $correctAnswer = -1;
        }
    }
@endphp



@extends('layouts.app')

@section('content')
<div class="container py-5">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $index => $error)
                    @if ($index < 10)
                        <li>{{ $error }}</li>
                    @endif
                @endforeach
                @if ($errors->count() > 10)
                    <li>Và {{ $errors->count() - 10 }} lỗi khác...</li>
                @endif
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.quizzes.questions.update', ['quiz' => $quiz->id, 'question' => $question->id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="question" class="form-label">Câu hỏi</label>
            <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror"
                   value="{{ old('question', $question->question) }}">
            @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự</label>
            <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror"
                   value="{{ old('order', $question->order) }}" min="1">
            @error('order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="answer_type" class="form-label">Loại câu trả lời</label>
            <select name="answer_type" id="answer_type" class="form-select @error('answer_type') is-invalid @enderror">
                <option value="multiple_choice" {{ old('answer_type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Lựa chọn nhiều</option>
                <option value="text_input" {{ old('answer_type', $question->type) == 'text_input' ? 'selected' : '' }}>Nhập văn bản</option>
                <option value="true_false" {{ old('answer_type', $question->type) == 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
            </select>
            @error('answer_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Multiple Choice --}}
        <div id="multiple-choice-answers" style="display: none;">
            <div class="mb-3">
                <label class="form-label">Các đáp án</label>
                <div id="answers-container">
                    @foreach(old('answers', $answers ?? []) as $index => $answer)
                        @php
                            $text = is_array($answer) ? $answer['text'] ?? $answer['answer'] ?? '' : $answer->answer;
                        @endphp
                        <div class="input-group mb-3">
                            <input type="text" name="answers[{{ $index }}][text]" class="form-control"
                                   placeholder="Đáp án {{ $index + 1 }}"
                                   value="{{ old("answers.$index.text", $text) }}">
                            <div class="input-group-text">
                                <input type="radio" name="correct_answer" value="{{ $index }}"
                                    {{ $correctAnswer == $index ? 'checked' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary" id="add-answer">Thêm đáp án</button>
                @error('correct_answer')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Text Input --}}
        <div id="text-input-answer" style="display: none;">
            <div class="mb-3">
                <label for="text_answer" class="form-label">Đáp án văn bản</label>
                <input type="text" name="text_answer" id="text_answer"
                       class="form-control @error('text_answer') is-invalid @enderror"
                       value="{{ old('text_answer', $answers[0]->answer ?? '') }}">
                @error('text_answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- True / False --}}
        <div id="true-false-answers" style="display: none;">
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Chọn đáp án đúng</label>
                <select name="correct_answer" id="correct_answer" class="form-select @error('correct_answer') is-invalid @enderror">
                    <option value="1" {{ $correctAnswer == 1 ? 'selected' : '' }}>Đúng</option>
                    <option value="0" {{ $correctAnswer == 0 ? 'selected' : '' }}>Sai</option>
                </select>
                @error('correct_answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Cập Nhật Câu Hỏi</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        const answerTypeSelect = document.getElementById('answer_type');
        const multipleChoiceAnswers = document.getElementById('multiple-choice-answers');
        const textInputAnswer = document.getElementById('text-input-answer');
        const trueFalseAnswers = document.getElementById('true-false-answers');

        function toggleAnswerSections() {
            const type = answerTypeSelect.value;
            multipleChoiceAnswers.style.display = 'none';
            textInputAnswer.style.display = 'none';
            trueFalseAnswers.style.display = 'none';

            if (type === 'multiple_choice') {
                multipleChoiceAnswers.style.display = 'block';
            } else if (type === 'text_input') {
                textInputAnswer.style.display = 'block';
            } else if (type === 'true_false') {
                trueFalseAnswers.style.display = 'block';
            }
        }

        answerTypeSelect.addEventListener('change', toggleAnswerSections);

        document.getElementById('add-answer').addEventListener('click', function () {
            const container = document.getElementById('answers-container');
            const answerInputs = container.querySelectorAll('input[type="text"]').length;
            if (answerInputs >= 10) {
                alert('Bạn chỉ có thể thêm tối đa 10 đáp án!');
                return;
            }
            const newAnswerInput = document.createElement('div');
            newAnswerInput.classList.add('input-group', 'mb-3');
            newAnswerInput.innerHTML = `
                <input type="text" name="answers[${answerInputs}][text]" class="form-control" placeholder="Đáp án ${answerInputs + 1}">
                <div class="input-group-text">
                    <input type="radio" name="correct_answer" value="${answerInputs}">
                </div>
            `;
            container.appendChild(newAnswerInput);
        });

        toggleAnswerSections();
    })();
</script>
@endsection
