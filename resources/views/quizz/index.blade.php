@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Bắt đầu Quiz: {{ $quiz->title }}</h3>
    <p>{{ $quiz->description }}</p>

    <div class="alert alert-info">
        Thời gian còn lại: <span id="time">00:00</span>
    </div>

    <form id="quiz-result-form" method="POST" action="{{ route('quizz.submit', $quiz->id) }}">
        @csrf
        <input type="hidden" name="score" id="final-score">
        <input type="hidden" name="answers" id="final-answers">
        <div id="quiz-container">
            @foreach($questions as $index => $question)
                <div class="card mb-3 question-block" data-index="{{ $index }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                    <div class="card-header">Câu {{ $index + 1 }} trên {{ $questions->count() }}</div>
                    <div class="card-body">
                        <p class="fw-bold">{{ $question->question }}</p>
                        @foreach($question->answers as $answer)
                            <button class="btn btn-outline-primary d-block mb-2 answer-btn"
                                    data-answer-id="{{ $answer->id }}"
                                    data-question-id="{{ $question->id }}"
                                    data-is-correct="{{ $answer->is_correct ? '1' : '0' }}"
                                    data-disabled="false">
                                {{ $answer->answer }}
                            </button>
                        @endforeach
                        <div class="result mt-2 fw-bold"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const totalQuestions = {{ $questions->count() }};
    let currentIndex = 0;
    let correctCount = 0;
    let timer = {{ $quiz->time_limit ?? 60 }} * 60;
    const display = document.getElementById('time');
    const selectedAnswers = [];
    let isSubmitting = false;

    const interval = setInterval(updateTimer, 1000);

    function updateTimer() {
        const minutes = String(Math.floor(timer / 60)).padStart(2, '0');
        const seconds = String(timer % 60).padStart(2, '0');
        display.textContent = `${minutes}:${seconds}`;

        if (--timer < 0) {
            clearInterval(interval);
            alert("Thời gian đã hết!");
            finishQuiz();
        }
    }

    function showQuestion(index) {
        document.querySelectorAll('.question-block').forEach(block => {
            block.style.display = block.dataset.index == index ? '' : 'none';
        });
    }

    function nextOrFinish() {
        if (currentIndex + 1 < totalQuestions) {
            currentIndex++;
            showQuestion(currentIndex);
        } else {
            finishQuiz();
        }
    }

    function finishQuiz() {
        if (isSubmitting) return;
        isSubmitting = true;
        clearInterval(interval);
        document.getElementById('quiz-container').style.display = 'none';

        // Ensure the form is submitted as POST
        document.getElementById('final-score').value = correctCount;
        document.getElementById('final-answers').value = JSON.stringify(selectedAnswers);
        document.getElementById('quiz-result-form').submit(); // Ensure this submits the form as POST
    }

    function checkAnswer(buttonElement) {
        return buttonElement.dataset.isCorrect === "1";
    }

    document.querySelectorAll('.answer-btn').forEach(button => {
        button.addEventListener('click', function () {
            if (this.dataset.disabled === "true") return;

            const answerId = this.dataset.answerId;
            const questionId = this.dataset.questionId;
            const container = this.closest('.question-block');
            const resultEl = container.querySelector('.result');

            container.querySelectorAll('.answer-btn').forEach(btn => {
                btn.disabled = true;
                btn.dataset.disabled = "true";
            });

            const isCorrect = checkAnswer(this);
            resultEl.textContent = isCorrect ? 'Đúng' : 'Sai';
            resultEl.classList.add(isCorrect ? 'text-success' : 'text-danger');

            if (isCorrect) correctCount++;

            selectedAnswers.push({
                question_id: questionId,
                answer_id: answerId,
                is_correct: isCorrect
            });

            setTimeout(nextOrFinish, 1000);
        });
    });
});
</script>
@endsection
