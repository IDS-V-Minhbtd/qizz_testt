@extends('layouts.app')

@section('content')
    <h2>Chỉnh sửa câu hỏi: {{ $quiz->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<form method="POST" action="{{ route('admin.quizzes.questions.update', [$quiz->id, $question->id]) }}">
    @csrf
    <input type="hidden" name="answer_type" value="multiple_choice">
    <input type="hidden" name="question_id" value="{{ $question->id }}">
    <div class="mb-3">
        <label for="question" class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
        <textarea name="question" id="question" rows="3" class="form-control">{{ old('question', $question->question) }}</textarea>
        @error('question')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
        <input type="number" name="order" id="order" class="form-control" value="{{ old('order', $question->order) }}" min="1">
        @error('order')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

   <div class="card mt-3>
        <div class="card-header">đáp án</div>
        <div class="card-body">
            {{-- Multiple Choice --}}
            <div id="mutiple-choice-answers">
                <h5>Đáp án (Trắc nghiệm)</h5>
                <div id="answers-wrapper">
                    @php
                        $oldAnswers = old('answers', $question->answers->pluck('text', 'id')->toArray());
                        $maxId = max(array_keys($oldAnswers)) ?: 2;
                    @endphp
                    @foreach ($oldAnswers as $id => $text)
                        <div class="input-group mb-2" data-answer-id="{{ $id }}">
                            <span class="input-group-text">Đáp án {{ $id }}</span>
                            <input type="text" name="answers[{{ $id }}][text]" class="form-control answer-input" placeholder="Nội dung đáp án" value="{{ $text }}" >
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


<div class="mb-3">
    <h5>Đáp án (Trắc nghiệm)</h5>
    <div id="answers-wrapper">
        @php
            $oldAnswers = old('answers', $question->answers->pluck('text', 'id')->toArray());
            $maxId = max(array_keys($oldAnswers)) ?: 2;
        @endphp
        @foreach ($oldAnswers as $id => $text)
            <div class="input-group mb-2" data-answer-id="{{ $id }}">
                <span class="input-group-text">Đáp án {{ $id }}</span>
                <input type="text" name="answers[{{ $id }}][text]" class="form-control answer-input" placeholder="Nội dung đáp án" value="{{ $text }}" >
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
    <div class="card mt-3">
        <div class="card-body">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Cập nhật
            </button>
            <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded',function()){
        const answersWrapper = document.getElementById('answers-wrapper');

    }



    document.addEventListener('DOMContentLoaded', function() {
        let answerId = {{ $maxId }};

        document.getElementById('btn-add-answer').addEventListener('click', function() {
            answerId++;
            const wrapper = document.getElementById('answers-wrapper');
            const newAnswer = document.createElement('div');
            newAnswer.className = 'input-group mb-2';
            newAnswer.setAttribute('data-answer-id', answerId);
            newAnswer.innerHTML = `
                <span class="input-group-text">Đáp án ${answerId}</span>
                <input type="text" name="answers[${answerId}][text]" class="form-control answer-input" placeholder="Nội dung đáp án">
                <button type="button" class="btn btn-danger btn-remove-answer">×</button>
            `;
            wrapper.appendChild(newAnswer);
        });

        document.getElementById('answers-wrapper').addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-answer')) {
                e.target.closest('.input-group').remove();
            }
        });
    });