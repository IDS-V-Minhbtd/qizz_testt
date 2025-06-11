
@extends('layouts.combined')

@section('content')
<div class="full-width-bg" style="background: linear-gradient(135deg, #1a1030, #2c1f3b); min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <h3 class="mb-5 text-center fw-bold display-5 animate__animated animate__fadeInDown" style="color: #ffffff; font-family: 'Inter', sans-serif;">
            Bắt đầu Quiz: {{ $quiz->name }}
        </h3>
        <p class="text-white mb-4" style="font-size: 1.1rem;">{{ $quiz->description }}</p>

        @if ($errors->any())
            <div class="alert alert-danger text-center animate__animated animate__fadeInUp rounded-3" role="alert" style="max-width: 600px; margin: 0 auto;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success text-center animate__animated animate__fadeInUp rounded-3" role="alert" style="max-width: 600px; margin: 0 auto;">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 text-white" style="font-size: 1.1rem;">
            <span>Thời gian còn lại: </span>
            <span id="timer" style="font-weight: bold; color: #00c6ff;">00:00</span>
        </div>

        <form id="quiz-form" method="POST" action="{{ route('quizz.submit', $quiz->id) }}">
            @csrf
            <input type="hidden" name="time_taken" id="time-taken" value="0">

            @foreach($questions as $index => $question)
                <div class="card mb-4 question-block animate__animated animate__fadeInUp" data-index="{{ $index }}" style="{{ $index === 0 ? '' : 'display:none;' }}; border-radius: 20px; background: linear-gradient(135deg, #6a11cb, #2575fc);">
                    <div class="card-header text-white" style="background: rgba(0, 0, 0, 0.2); border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        Câu {{ $index + 1 }} / {{ $questions->count() }}
                    </div>
                    <div class="card-body text-white p-4">
                        <p class="fw-bold" style="font-size: 1.2rem;">{{ $question->question }}</p>
                        @foreach($question->answers as $answer)
                            <div class="form-check mb-3">
                                <input class="form-check-input"
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $answer->id }}"
                                    id="answer-{{ $answer->id }}"
                                    >
                                <label class="form-check-label" for="answer-{{ $answer->id }}" style="font-size: 1rem;">
                                    {{ $answer->answer }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" id="next-btn" style="transition: all 0.3s ease;">
                    Câu tiếp theo
                </button>
                <button type="submit" class="btn btn-success rounded-pill px-4 d-none" id="submit-btn" style="transition: all 0.3s ease;">
                    Nộp bài
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Full-width background */
    .full-width-bg {
        background: linear-gradient(135deg, #1a1030, #2c1f3b);
        min-height: 100vh;
        padding: 40px 0;
        color: #ffffff;
        font-family: 'Inter', sans-serif;
    }

    .text-white {
        color: #ffffff !important;
    }

    .card {
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary, .btn-success {
        font-weight: 600;
        padding: 10px 20px;
    }

    .btn-secondary:hover {
        background: #6c757d;
        transform: scale(1.03);
    }

    .btn-success:hover {
        background: #28a745;
        transform: scale(1.03);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .display-5 {
            font-size: 1.8rem;
        }
        .card-body p {
            font-size: 1rem;
        }
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    /* Adjust alert style */
    .alert {
        border-radius: 10px;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
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