@extends('adminlte::page')

@section('title', 'Cập nhật câu hỏi')

@section('content_header')
    <h1>Cập nhật câu hỏi: {{ $quiz->name }}</h1>
@endsection

@section('content')
@php
    $questionType = old('answer_type', $question->type);
    $answers = collect($answers ?? [])->map(function ($a) {
        return [
            'text' => is_array($a) ? ($a['text'] ?? $a['answer'] ?? '') : $a->answer,
            'is_correct' => is_array($a) ? ($a['is_correct'] ?? false) : $a->is_correct,
        ];
    });

    $correctAnswer = old('correct_answer');
    if (is_null($correctAnswer)) {
        if ($questionType === 'multiple_choice') {
            $correctAnswer = $answers->search(fn($a) => $a['is_correct']);
        } elseif ($questionType === 'true_false') {
            $correctAnswer = $answers->firstWhere('is_correct', true)['text'] === 'Đúng' ? '1' : '0';
        } elseif ($questionType === 'text_input') {
            $correctAnswer = $answers->first()['text'] ?? '';
        }
    }
@endphp

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
    @method('PUT')

    {{-- Câu hỏi chính --}}
    <div class="card">
        <div class="card-header">Thông tin câu hỏi</div>
        <div class="card-body">
            <div class="form-group">
                <label for="question">Câu hỏi <span class="text-danger">*</span></label>
                <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror"
                       value="{{ old('question', $question->question) }}" required>
                @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="order">Thứ tự <span class="text-danger">*</span></label>
                <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror"
                       value="{{ old('order', $question->order) }}" required min="1">
                @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="answer_type">Loại câu trả lời <span class="text-danger">*</span></label>
                <select name="answer_type" id="answer_type" class="form-control @error('answer_type') is-invalid @enderror" required>
                    <option value="multiple_choice" {{ $questionType === 'multiple_choice' ? 'selected' : '' }}>Lựa chọn nhiều</option>
                    <option value="text_input" {{ $questionType === 'text_input' ? 'selected' : '' }}>Nhập văn bản</option>
                    <option value="true_false" {{ $questionType === 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
                </select>
                @error('answer_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- Đáp án --}}
    <div class="card mt-3">
        <div class="card-header">Đáp án</div>
        <div class="card-body">
            {{-- Multiple Choice --}}
            <div id="multiple-choice-section" style="display: none;">
                <div id="answers-container">
                    @foreach($answers as $index => $a)
                        <div class="input-group mb-2">
                            <input type="text" name="answers[{{ $index }}][text]" class="form-control"
                                   value="{{ old("answers.$index.text", $a['text']) }}" placeholder="Đáp án {{ $index + 1 }}" required>
                            <div class="input-group-text">
                                <input type="radio" name="correct_answer" value="{{ $index }}"
                                       {{ $correctAnswer == $index ? 'checked' : '' }}>
                                <span class="ml-2">Đúng</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="add-answer">
                    <i class="fas fa-plus"></i> Thêm đáp án
                </button>
                @error('correct_answer') <div class="text-danger mt-2">{{ $message }}</div> @enderror
            </div>

            {{-- Text Input --}}
            <div id="text-input-section" style="display: none;">
                <div class="form-group">
                    <label for="text_answer">Đáp án chính xác</label>
                    <input type="text" name="text_answer" id="text_answer" class="form-control"
                           value="{{ old('text_answer', $correctAnswer) }}">
                    @error('text_answer') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- True/False --}}
            <div id="true-false-section" style="display: none;">
                <div class="form-group">
                    <label>Chọn đáp án đúng</label>
                    <select name="correct_answer" class="form-control" required>
                        <option value="1" {{ $correctAnswer == '1' ? 'selected' : '' }}>Đúng</option>
                        <option value="0" {{ $correctAnswer == '0' ? 'selected' : '' }}>Sai</option>
                    </select>
                    @error('correct_answer') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Nút submit --}}
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

@section('js')
<script>
    const answerType = document.getElementById('answer_type');
    const multipleChoiceSection = document.getElementById('multiple-choice-section');
    const textInputSection = document.getElementById('text-input-section');
    const trueFalseSection = document.getElementById('true-false-section');

    function toggleSections() {
        const type = answerType.value;
        multipleChoiceSection.style.display = type === 'multiple_choice' ? 'block' : 'none';
        textInputSection.style.display = type === 'text_input' ? 'block' : 'none';
        trueFalseSection.style.display = type === 'true_false' ? 'block' : 'none';
    }

    answerType.addEventListener('change', toggleSections);
    toggleSections();

    // Add answer dynamically
    document.getElementById('add-answer')?.addEventListener('click', function () {
        const container = document.getElementById('answers-container');
        const index = container.children.length;
        if (index >= 10) {
            alert('Chỉ được tối đa 10 đáp án');
            return;
        }

        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" name="answers[${index}][text]" class="form-control" placeholder="Đáp án ${index + 1}" required>
            <div class="input-group-text">
                <input type="radio" name="correct_answer" value="${index}">
                <span class="ml-2">Đúng</span>
            </div>
        `;
        container.appendChild(div);
    });
</script>
@endsection
