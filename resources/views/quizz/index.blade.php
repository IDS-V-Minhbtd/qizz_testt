@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Bắt đầu Quiz: {{ $quiz->name }}</h3>
    <p>{{ $quiz->description }}</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <span>Thời gian còn lại: </span>
        <span id="timer">00:00</span>
    </div>

    <form id="quiz-form" method="POST" action="{{ route('quizz.submit', $quiz->id) }}">
        @csrf
        <input type="hidden" name="time_taken" id="time-taken" value="0">

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
                                id="answer-{{ $answer->id }}"
                                required>
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
    const timeLimit = {{ $quiz->time_limit * 60 }}; // Convert minutes to seconds
    let currentIndex = 0;
    let startTime = Date.now();
    let timeRemaining = timeLimit;

    const questionBlocks = document.querySelectorAll('.question-block');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const quizForm = document.getElementById('quiz-form');
    const timerDisplay = document.getElementById('timer');
    const timeTakenInput = document.getElementById('time-taken');

    function updateTimer() {
        timeRemaining = Math.max(0, timeLimit - Math.floor((Date.now() - startTime) / 1000));
        const minutes = Math.floor(timeRemaining / 60).toString().padStart(2, '0');
        const seconds = (timeRemaining % 60).toString().padStart(2, '0');
        timerDisplay.textContent = `${minutes}:${seconds}`;
        timeTakenInput.value = timeLimit - timeRemaining;

        if (timeRemaining <= 0) {
            quizForm.submit();
        }
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    const showQuestion = index => {
        questionBlocks.forEach((block, i) => {
            block.style.display = i === index ? '' : 'none';
        });

        if (index === totalQuestions - 1) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        }
    };

    nextBtn.addEventListener('click', () => {
        const currentBlock = questionBlocks[currentIndex];
        const selectedAnswer = currentBlock.querySelector('input[type="radio"]:checked');
        if (!selectedAnswer) {
            alert('Vui lòng chọn một đáp án trước khi chuyển câu tiếp theo!');
            return;
        }

        if (currentIndex < totalQuestions - 1) {
            currentIndex++;
            showQuestion(currentIndex);
        }
    });

    quizForm.addEventListener('submit', function(event) {
        timeTakenInput.value = timeLimit - timeRemaining; // Update time_taken before submission
        let allAnswered = true;

        questionBlocks.forEach((block) => {
            const selectedAnswer = block.querySelector('input[type="radio"]:checked');
            if (!selectedAnswer) {
                allAnswered = false;
            }
        });

        if (!allAnswered) {
            event.preventDefault();
            alert('Vui lòng trả lời tất cả các câu hỏi!');
        }
    });

    showQuestion(0);
});
</script>
@endsection