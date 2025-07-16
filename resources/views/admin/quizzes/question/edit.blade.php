@extends('layouts.app')

@section('content_header')
    <h1 class="m-0">Edit Question: {{ $question->question }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.edit', $quiz->id) }}">{{ $quiz->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Question</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container" style="max-width: 800px">
    <h2 class="mb-4">Chỉnh sửa câu hỏi: {{ $quiz->name }}</h2>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@php
    $oldAnswers = collect(old('answers'))
        ->filter(fn($item) => is_array($item) && isset($item['text']))
        ->values()
        ->toArray();

    if (empty($oldAnswers)) {
        $oldAnswers = $question->answers->pluck('answer')
            ->map(fn($text) => ['text' => $text])
            ->values()
            ->toArray();
    }

    while (count($oldAnswers) < 2) {
        $oldAnswers[] = ['text' => ''];
    }

    $selectedAnswerId = old('correct_answer', $question->answers->search(fn($a) => $a->is_correct) ?? 0);
@endphp

    <form method="POST" action="{{ route('admin.quizzes.questions.update', [$quiz->id, $question->id]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

   {{-- Ảnh minh họa --}}
<div class="mb-3">
    <label for="picture" class="form-label">Ảnh minh họa</label>

    {{-- Nếu có ảnh hiện tại thì hiển thị --}}
    <div class="mb-2">
        @if ($question->picture)
            <img src="{{ asset('storage/' . $question->picture) }}" alt="Ảnh hiện tại"
                 class="img-fluid rounded border" style="max-height: 200px;">
        @else
            <div class="text-muted fst-italic">Chưa có ảnh minh họa</div>
        @endif
    </div>

    <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
    @error('picture')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>


        <input type="hidden" name="answer_type" value="multiple_choice">
        <input type="hidden" name="question_id" value="{{ $question->id }}">

        {{-- Câu hỏi --}}
        <div class="mb-3">
            <label for="question" class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
            <textarea name="question" id="question" rows="3" class="form-control">{{ old('question', $question->question) }}</textarea>
            @error('question')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Thứ tự --}}
        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
            <input type="number" name="order" id="order" class="form-control" value="{{ old('order', $question->order) }}" min="1">
            @error('order')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Đáp án --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">Đáp án (Trắc nghiệm)</div>
            <div class="card-body">
                <div id="answers-wrapper">
                    @foreach ($oldAnswers as $id => $answer)
                        <div class="row g-2 align-items-center mb-2" data-answer-id="{{ $id }}">
                            <div class="col-auto">
                                <label class="form-label mb-0">Đáp án {{ $loop->iteration }}</label>
                            </div>
                            <div class="col">
                                <input type="text" name="answers[{{ $id }}][text]" class="form-control answer-input"
                                       value="{{ $answer['text'] ?? '' }}" placeholder="Nội dung đáp án">
                            </div>
                            @if ($loop->index >= 2)
                                <div class="col-auto">
                                    <button type="button" class="btn btn-outline-danger btn-remove-answer">×</button>
                                </div>
                            @endif
                        </div>
                        @error("answers.{$id}.text")
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
                    @endforeach
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btn-add-answer">+ Thêm đáp án</button>

                @error('answers')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror

                {{-- Đáp án đúng --}}
                <div class="mt-4">
                    <label for="correct_answer" class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                    <select name="correct_answer" id="correct_answer" class="form-select" data-selected="{{ $selectedAnswerId }}">
                        @foreach ($oldAnswers as $id => $answer)
                            <option value="{{ $id }}" {{ $selectedAnswerId == $id ? 'selected' : '' }}>
                                Đáp án {{ $answer['text'] ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('correct_answer')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let answerId = {{ count($oldAnswers) }};
    const wrapper = document.getElementById('answers-wrapper');
    const correctSelect = document.getElementById('correct_answer');

    function updateAnswerLabelsAndOptions() {
        const rows = wrapper.querySelectorAll('.row[data-answer-id]');
        correctSelect.innerHTML = '';
        rows.forEach((row, index) => {
            const label = row.querySelector('label');
            const input = row.querySelector('input');
            const id = index;

            row.setAttribute('data-answer-id', id);
            input.setAttribute('name', `answers[${id}][text]`);

            if (label) {
                label.textContent = 'Đáp án ' + (index + 1);
            }

            const option = document.createElement('option');
            option.value = id;
            option.textContent = 'Đáp án ' + (input.value || '');
            if (parseInt(correctSelect.getAttribute('data-selected')) === id) {
                option.selected = true;
            }
            correctSelect.appendChild(option);

            input.addEventListener('input', () => {
                const opt = correctSelect.querySelector(`option[value="${id}"]`);
                if (opt) opt.textContent = 'Đáp án ' + input.value;
            });
        });
    }

    document.getElementById('btn-add-answer').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2';
        row.innerHTML = `
            <div class="col-auto">
                <label class="form-label mb-0">Đáp án</label>
            </div>
            <div class="col">
                <input type="text" class="form-control answer-input" placeholder="Nội dung đáp án">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-danger btn-remove-answer">×</button>
            </div>
        `;
        wrapper.appendChild(row);
        updateAnswerLabelsAndOptions();
    });

    wrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-answer')) {
            e.target.closest('.row').remove();
            updateAnswerLabelsAndOptions();
        }
    });

    correctSelect.setAttribute('data-selected', correctSelect.value);
    updateAnswerLabelsAndOptions();
});
</script>
@endsection
