@extends('layouts.combined')

@section('content')
<div class="quiz-bg d-flex align-items-center justify-content-center py-5">
    <div class="quiz-main-card shadow-lg rounded-4 p-4 p-md-5 bg-white" style="width:100%; max-width: 700px;">
        <div class="mb-4">
            <h3 class="fw-bold text-center text-gradient mb-2">Quiz: {{ $quiz->name }}</h3>
            <p class="text-center text-muted">{{ $quiz->description }}</p>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="badge bg-primary fs-6 px-3 py-2">
                    <i class="bi bi-clock me-1"></i>
                    Thời gian: <span id="timer" class="fw-bold">00:00</span>
                </span>
                <span class="badge bg-info fs-6 px-3 py-2">
                    <span id="progress-current">1</span>/<span id="progress-total">{{ $questions->count() }}</span>
                </span>
            </div>
            <div class="progress mt-3" style="height: 8px;">
                <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
            </div>
        </div>

        <form id="quiz-form" method="POST" action="{{ route('quizz.submit', $quiz->id) }}">
            @csrf
            <input type="hidden" name="time_taken" id="time-taken" value="0">

            @foreach($questions as $index => $question)
            <div class="question-block" data-index="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                <div class="mb-3">
                    <span class="badge bg-secondary me-2">Câu {{ $loop->iteration }}</span>
                    <span class="fw-semibold fs-5">{{ $question->question }}</span>
                </div>
                <div class="row g-3">
                    @foreach($question->answers as $answer)
                    <div class="col-md-6">
                        <label class="answer-card">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="d-none">
                            <div class="card-content d-flex align-items-center p-3 rounded-3">
                                <div class="check-icon me-3"><i class="bi bi-check-circle-fill"></i></div>
                                <div class="answer-text flex-grow-1">{{ $answer->answer }}</div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-end mt-4">
                <button type="button" id="next-btn" class="btn btn-outline-primary rounded-pill px-4 me-2">Câu tiếp theo</button>
                <button type="submit" id="submit-btn" class="btn btn-gradient rounded-pill px-4 fw-bold d-none">
                    <i class="bi bi-send me-1"></i> Nộp bài
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
.quiz-bg {
    min-height: 100vh;
    background: linear-gradient(135deg, #1f1147, #312a72);
}
.quiz-main-card {
    background: #fffdfc;
    border: 2px solid #d7d4ec;
}
.text-gradient {
    background: linear-gradient(90deg, #6366f1, #38bdf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.answer-card {
    cursor: pointer;
    display: block;
    user-select: none;
}
.card-content {
    background: #f3f4f6;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.card-content:hover {
    background: #e0f2fe;
    border-color: #3b82f6;
}
.answer-card.selected .card-content {
    background: #dbeafe !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.3);
}
.answer-card.selected .check-icon {
    opacity: 1;
    color: #2563eb;
    transform: scale(1.2);
}
.check-icon {
    font-size: 1.4rem;
    color: #ccc;
    opacity: 0;
    transition: 0.3s ease;
}
.answer-text {
    font-size: 1rem;
}
.btn-gradient {
    background: linear-gradient(90deg, #38bdf8 0%, #6366f1 100%);
    color: #fff;
    border: none;
}
.btn-gradient:hover {
    background: linear-gradient(90deg, #6366f1 0%, #38bdf8 100%);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const total = {{ $questions->count() }};
    const timeLimit = {{ $quiz->time_limit * 60 }};
    let currentIndex = 0;
    let startTime = Date.now();

    const blocks = document.querySelectorAll('.question-block');
    const timerEl = document.getElementById('timer');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const timeTakenInput = document.getElementById('time-taken');
    const form = document.getElementById('quiz-form');
    const progressCurrent = document.getElementById('progress-current');
    const progressBar = document.getElementById('progress-bar');

    function updateProgress() {
        progressCurrent.textContent = currentIndex + 1;
        progressBar.style.width = ((currentIndex + 1) / total * 100) + '%';
    }

    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const remain = Math.max(0, timeLimit - elapsed);
        const min = String(Math.floor(remain / 60)).padStart(2, '0');
        const sec = String(remain % 60).padStart(2, '0');
        timerEl.textContent = `${min}:${sec}`;
        timeTakenInput.value = elapsed;
        if (remain <= 0) form.submit();
    }

    setInterval(updateTimer, 1000);

    // Chọn đáp án
    document.querySelectorAll('.answer-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const block = this.closest('.question-block');
            block.querySelectorAll('.answer-card').forEach(label => label.classList.remove('selected'));
            const label = this.closest('.answer-card');
            if (this.checked && label) label.classList.add('selected');
        });
    });

    nextBtn.addEventListener('click', () => {
        const current = blocks[currentIndex];
        const selected = current.querySelector('input[type="radio"]:checked');
        if (!selected) {
            alert('Vui lòng chọn một đáp án!');
            return;
        }
        current.style.display = 'none';
        currentIndex++;
        if (currentIndex < total) {
            blocks[currentIndex].style.display = 'block';
            updateProgress();
        }
        if (currentIndex === total - 1) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        }
    });

    form.addEventListener('submit', function (e) {
        let ok = true;
        blocks.forEach(block => {
            if (!block.querySelector('input[type="radio"]:checked')) {
                ok = false;
            }
        });
        if (!ok) {
            e.preventDefault();
            alert('Bạn chưa trả lời hết các câu hỏi!');
        }
    });

    updateProgress();
});
</script>
@endsection

