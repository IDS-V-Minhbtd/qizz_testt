@extends('layouts.app')

@section('content')
<div class="container">
    <h3>{{ $quiz->title }}</h3>
    <p>{{ $quiz->description }}</p>

    <div id="questions-container">
        @foreach($quiz->questions as $index => $question)
            <div class="card mb-3 question-block" data-question-id="{{ $question->id }}">
                <div class="card-header">
                    Câu {{ $index + 1 }}: {{ $question->question }}
                </div>
                <div class="card-body">
                    @foreach($question->answers as $answer)
                        <button
                            class="btn btn-outline-primary d-block mb-2 answer-btn"
                            data-answer-id="{{ $answer->id }}"
                            data-question-id="{{ $question->id }}"
                        >
                            {{ $answer->answer }}
                        </button>
                    @endforeach
                    <div class="result mt-2 fw-bold"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll('.answer-btn');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const answerId = this.dataset.answerId;
            const questionId = this.dataset.questionId;
            const container = this.closest('.question-block');
            const resultEl = container.querySelector('.result');

            // Disable all buttons in this question block
            container.querySelectorAll('.answer-btn').forEach(btn => btn.disabled = true);

            // Gửi API kiểm tra câu trả lời
            fetch('/api/check-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ answer_id: answerId, question_id: questionId })
            })
            .then(res => res.json())
            .then(data => {
                resultEl.textContent = data.message;
                resultEl.classList.add(data.correct ? 'text-success' : 'text-danger');
            })
            .catch(() => {
                resultEl.textContent = 'Đã có lỗi xảy ra';
                resultEl.classList.add('text-danger');
            });
        });
    });
});
</script>
@endsection
