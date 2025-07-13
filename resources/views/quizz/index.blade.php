@extends('layouts.combined')

@section('content')
<div class="full-width-bg py-5">
    <div class="container">
        <h3 class="mb-4 text-center fw-bold text-white">Bắt đầu Quiz: {{ $quiz->name }}</h3>
        <p class="text-white text-center">{{ $quiz->description }}</p>
        <p class="text-white text-center">Thời gian còn lại: <span id="timer" class="fw-bold text-info">00:00</span></p>

        <form id="quiz-form" method="POST" action="{{ route('quizz.submit', $quiz->id) }}">
            @csrf
            <input type="hidden" name="time_taken" id="time-taken" value="0">

            @foreach($questions as $index => $question)
            <div class="card mb-4 question-block" data-index="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }}; border-radius: 20px; background: linear-gradient(135deg, #6a11cb, #2575fc);">
                <div class="card-header text-white">
                    Câu {{ $index + 1 }} / {{ $questions->count() }}
                </div>
                <div class="card-body text-white p-4">
                    <p class="fw-bold">{{ $question->question }}</p>
                    <div class="row g-3">
                        @foreach($question->answers as $answer)
                        <div class="col-md-6">
                            <div class="answer-option p-3 rounded-3"
                                style="background: rgba(255, 255, 255, 0.1); cursor: pointer;"
                                onclick="selectAnswer(this)">
                                <input type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $answer->id }}"
                                    id="answer-{{ $answer->id }}"
                                    class="d-none">
                                <label for="answer-{{ $answer->id }}" class="d-block mb-0">{{ $answer->answer }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="text-end">
                <button type="button" id="next-btn" class="btn btn-secondary rounded-pill px-4">Câu tiếp theo</button>
                <button type="submit" id="submit-btn" class="btn btn-success rounded-pill px-4 d-none">Nộp bài</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .full-width-bg {
        background: linear-gradient(135deg, #1a1030, #2c1f3b);
        min-height: 100vh;
    }

    .answer-option {
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: 0.3s ease;
    }

    .answer-option:hover {
        background: rgba(255, 255, 255, 0.2) !important;
    }

    .answer-option.selected {
        border: 2px solid #00c6ff !important;
        background: rgba(255, 255, 255, 0.2);
    }
</style>
@endsection

@section('scripts')
<script>
let currentIndex = 0;
const questionBlocks = document.querySelectorAll('.question-block');
const nextBtn = document.getElementById('next-btn');
const submitBtn = document.getElementById('submit-btn');
const timerEl = document.getElementById('timer');
const timeTakenInput = document.getElementById('time-taken');

const totalQuestions = questionBlocks.length;
const timeLimit = {{ $quiz->time_limit * 60 }};
const startTime = Date.now();

function updateTimer() {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const remaining = Math.max(0, timeLimit - elapsed);
    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
    const s = String(remaining % 60).padStart(2, '0');
    timerEl.textContent = `${m}:${s}`;
    timeTakenInput.value = elapsed;
    if (remaining <= 0) document.getElementById('quiz-form').submit();
}
setInterval(updateTimer, 1000);

// Highlight chọn đáp án
function selectAnswer(el) {
    const block = el.closest('.question-block');
    block.querySelectorAll('.answer-option').forEach(opt => opt.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
}

nextBtn.addEventListener('click', () => {
    const currentBlock = questionBlocks[currentIndex];
    const selected = currentBlock.querySelector('input[type="radio"]:checked');
    if (!selected) {
        alert('Vui lòng chọn một đáp án trước khi tiếp tục!');
        return;
    }

    currentBlock.style.display = 'none';
    currentIndex++;
    if (currentIndex < totalQuestions) {
        questionBlocks[currentIndex].style.display = 'block';
    }

    if (currentIndex === totalQuestions - 1) {
        nextBtn.classList.add('d-none');
        submitBtn.classList.remove('d-none');
    }
});

document.getElementById('quiz-form').addEventListener('submit', function (e) {
    let allAnswered = true;
    questionBlocks.forEach(block => {
        if (!block.querySelector('input[type="radio"]:checked')) {
            allAnswered = false;
        }
    });

    if (!allAnswered) {
        e.preventDefault();
        alert('Vui lòng trả lời tất cả các câu hỏi!');
    }
});
</script>
@endsection
