@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Bắt đầu Quiz: {{ $quiz->name }}</h3>
    <p>{{ $quiz->description }}</p>

    <form id="quiz-form" method="POST" action="{{ route('quizz.submit', $quiz->id) }}">
        @csrf

        @foreach($questions as $index => $question)
            <div class="card mb-3 question-block" data-index="{{ $index }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                <div class="card-header">Câu {{ $index + 1 }} / {{ $questions->count() }}</div>
                <div class="card-body">
                    <p class="fw-bold">{{ $question->question }}</p>
                    @foreach($question->answers as $answer)
                        <div class="form-check mb-2">
                            <input class="form-check-input"
                                type="radio"
                                name="answers[{{ $question->id }}]"
                                value="{{ $answer->id }}"
                                id="answer-{{ $answer->id }}">
                            <label class="form-check-label" for="answer-{{ $answer->id }}">
                                {{ $answer->answer }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="text-end mt-4">
            <button type="button" class="btn btn-secondary" id="next-btn">Câu tiếp theo</button>
            <button type="submit" class="btn btn-success d-none" id="submit-btn">Nộp bài</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const totalQuestions = {{ $questions->count() }};
    let currentIndex = 0;

    const questionBlocks = document.querySelectorAll('.question-block');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');

    const showQuestion = index => {
        questionBlocks.forEach((block, i) => {
            block.style.display = (i === index) ? '' : 'none';
        });

        if (index === totalQuestions - 1) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        }
    };

    nextBtn.addEventListener('click', () => {
        if (currentIndex < totalQuestions - 1) {
            currentIndex++;
            showQuestion(currentIndex);
        }
    });

    showQuestion(0);
});
</script>
@endsection
