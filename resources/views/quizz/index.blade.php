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
                    @if ($question->picture)
    <div class="mb-3 text-center">
        <img src="{{ asset('storage/' . $question->picture) }}" class="img-fluid rounded border border-2 shadow-sm" style="max-height: 250px;">

    </div>
    @endif

                <div class="row g-3 align-items-center">
                    @foreach($question->answers as $answer)
                    <div class="col-md-6">
                        <label class="answer-card d-flex align-items-center">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="me-2">
                            <div class="card-content d-inline-flex align-items-center p-2 rounded-3 flex-grow-1">
                                <span class="answer-text">{{ $answer->answer }}</span>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="prev-btn" class="btn btn-outline-secondary rounded-pill px-4" style="display: none;">Quay lại</button>
                <div>
                    <button type="button" id="next-btn" class="btn btn-outline-primary rounded-pill px-4 me-2">Câu tiếp theo</button>
                    <button type="submit" id="submit-btn" class="btn btn-gradient rounded-pill px-4 fw-bold d-none">
                        <i class="bi bi-send me-1"></i> Nộp bài
                    </button>
                </div>
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
    border-radius: 16px;
}
.text-gradient {
    background: linear-gradient(90deg, #6366f1, #38bdf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.answer-card {
    cursor: pointer;
    display: flex;
    align-items: center;
    width: 100%;
    user-select: none;
}
.card-content {
    background: #f3f4f6;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    width: 100%;
}
.card-content:hover {
    background: #e0f2fe;
    border-color: #3b82f6;
}
.col-md-6.selected {
    background: transparent; /* Loại bỏ nền cho col-md-6, áp dụng vào card-content */
}
.col-md-6.selected .card-content {
    background: #dbeafe !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.3);
}
.answer-text {
    font-size: 1rem;
    color: #333;
}
input[type="radio"] {
    margin-right: 10px;
    transform: scale(1.3);
    accent-color: #2563eb;
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
    const prevBtn = document.getElementById('prev-btn');
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

    // Hàm đánh dấu toàn bộ đáp án đã chọn (Cách 2)
    function markAllSelectedAnswers() {
        document.querySelectorAll('.question-block').forEach(block => {
            block.querySelectorAll('.col-md-6').forEach(col => {
                const input = col.querySelector('input[type="radio"]');
                if (input.checked) {
                    col.classList.add('selected');
                } else {
                    col.classList.remove('selected');
                }
            });
        });
    }

    setInterval(updateTimer, 1000);

    // Cách 1: Thêm class selected khi chọn đáp án trong câu hỏi hiện tại
    document.querySelectorAll('.answer-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const block = this.closest('.question-block');
            block.querySelectorAll('.col-md-6').forEach(col => col.classList.remove('selected'));
            const col = this.closest('.col-md-6');
            if (this.checked && col) {
                col.classList.add('selected');
                markAllSelectedAnswers(); // Gọi Cách 2 để cập nhật toàn bộ
            }
        });
    });

    // Chuyển câu tiếp theo
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
            markAllSelectedAnswers(); // Cập nhật đánh dấu cho tất cả câu hỏi
            prevBtn.style.display = 'inline-block';
        }
        if (currentIndex === total - 1) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        }
    });

    // Quay lại câu trước
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            blocks[currentIndex].style.display = 'none';
            currentIndex--;
            blocks[currentIndex].style.display = 'block';
            updateProgress();
            markAllSelectedAnswers(); // Cập nhật đánh dấu khi quay lại
            prevBtn.style.display = currentIndex === 0 ? 'none' : 'inline-block';
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }
    });

    // Xử lý nộp bài
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
        } else {
            markAllSelectedAnswers(); // Đánh dấu lại trước khi nộp
        }
    });

    // Đánh dấu ban đầu khi tải trang
    markAllSelectedAnswers();
    updateProgress();
});
</script>
@endsection