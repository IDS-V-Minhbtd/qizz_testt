@extends('adminlte::page')

@section('title', 'Cập nhật câu hỏi')

@section('content_header')
    <h1>Cập nhật câu hỏi: {{ $quiz->name }}</h1>
@endsection

@section('content')
@php
    $questionType = old('answer_type', $question->type); // giữ nguyên lựa chọn
    $answers = collect($answers ?? [])->map(function ($a) {
        return [
            'id' => $a['id'] ?? null,
            'text' => $a['answer'] ?? $a['text'] ?? '',
            'is_correct' => isset($a['is_correct']) ? (int)$a['is_correct'] : 0,
        ];
    });

    $correctIndexOld = old('correct_answer');
    $correctIndex = is_null($correctIndexOld)
        ? $answers->search(fn($a) => $a['is_correct'] === 1)
        : (int) $correctIndexOld;
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

    <div class="card mt-3">
        <div class="card-header">Đáp án</div>
        <div class="card-body">
            <div id="multiple-choice-section" style="display: {{ $questionType === 'multiple_choice' ? 'block' : 'none' }};">
                <div id="answers-container">
                    @foreach($answers as $index => $answer)
                        <div class="input-group mb-2" data-index="{{ $index }}">
                            <input type="text" name="answers[{{ $index }}][text]" class="form-control"
                                   value="{{ old("answers.$index.text", $answer['text']) }}" placeholder="Đáp án {{ $index + 1 }}" required>
                            <input type="hidden" name="answers[{{ $index }}][id]" value="{{ $answer['id'] ?? '' }}">
                            <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer['is_correct'] }}" class="is-correct-hidden">
                            <div class="input-group-text">
                                <input type="radio" name="correct_answer"
                                       value="{{ $index }}"
                                       {{ $correctIndex === $index ? 'checked' : '' }}>
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

            <div id="text-input-section" style="display: {{ $questionType === 'text_input' ? 'block' : 'none' }};">
                <div class="form-group">
                    <label for="text_answer">Đáp án chính xác</label>
                    <input type="text" name="text_answer" id="text_answer" class="form-control"
                           value="{{ old('text_answer', $answers[0]['text'] ?? '') }}">
                    @error('text_answer') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            <div id="true-false-section" style="display: {{ $questionType === 'true_false' ? 'block' : 'none' }};">
                <div class="form-group">
                    <label>Chọn đáp án đúng</label>
                    <select name="correct_answer" class="form-control" required>
                        <option value="1" {{ old('correct_answer', $answers->firstWhere('is_correct', 1)['text'] ?? '') === 'Đúng' ? 'selected' : '' }}>Đúng</option>
                        <option value="0" {{ old('correct_answer', $answers->firstWhere('is_correct', 1)['text'] ?? '') === 'Sai' ? 'selected' : '' }}>Sai</option>
                    </select>
                    @error('correct_answer') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

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

    document.querySelectorAll('input[name="correct_answer"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const selectedIndex = parseInt(this.value);
            const answersContainer = document.getElementById('answers-container');
            if (!answersContainer) return;
            answersContainer.querySelectorAll('.input-group').forEach(div => {
                const idx = parseInt(div.getAttribute('data-index'));
                const hiddenInput = div.querySelector('.is-correct-hidden');
                hiddenInput.value = (idx === selectedIndex) ? 1 : 0;
            });
        });
    });

    document.getElementById('add-answer')?.addEventListener('click', () => {
        const answersContainer = document.getElementById('answers-container');
        const newIndex = answersContainer.children.length;

        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.setAttribute('data-index', newIndex);

        div.innerHTML = `
            <input type="text" name="answers[${newIndex}][text]" class="form-control" placeholder="Đáp án ${newIndex + 1}" required>
            <input type="hidden" name="answers[${newIndex}][id]" value="">
            <input type="hidden" name="answers[${newIndex}][is_correct]" value="0" class="is-correct-hidden">
            <div class="input-group-text">
                <input type="radio" name="correct_answer" value="${newIndex}">
                <span class="ml-2">Đúng</span>
            </div>
        `;
        answersContainer.appendChild(div);

        div.querySelector('input[type=radio]').addEventListener('change', function () {
            const selectedIndex = parseInt(this.value);
            answersContainer.querySelectorAll('.input-group').forEach(div2 => {
                const idx = parseInt(div2.getAttribute('data-index'));
                const hiddenInput = div2.querySelector('.is-correct-hidden');
                hiddenInput.value = (idx === selectedIndex) ? 1 : 0;
            });
        });
    });

    toggleSections(); // Gọi lúc đầu
</script>
@endsection
