@extends('layouts.combined')

@section('content')
<div class="quiz-bg d-flex align-items-center justify-content-center py-5">
    <div class="quiz-main-card shadow-lg rounded-4 p-4 p-md-5 bg-white" style="width:100%; max-width: 700px;">
        <div class="mb-4">
            <h3 class="fw-bold text-center text-gradient mb-2">Flashcards: {{ $lesson->title ?? $lesson->name }}</h3>
            @if($lesson->description)
                <p class="text-center text-muted">{{ $lesson->description }}</p>
            @endif
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="badge bg-info fs-6 px-3 py-2">
                    <span id="progress-current">1</span>/<span id="progress-total">{{ $flashcards->count() }}</span>
                </span>
            </div>
            <div class="progress mt-3" style="height: 8px;">
                <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
            </div>
        </div>
        <div id="flashcard-container">
            @foreach($flashcards as $index => $flashcard)
            <div class="flashcard-block" data-index="{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                <div class="flashcard-flip mx-auto mb-4" tabindex="0">
                    <div class="flashcard-inner">
                        <div class="flashcard-front d-flex align-items-center justify-content-center">
                            <span class="fs-4">{{ $flashcard->front }}</span>
                        </div>
                        <div class="flashcard-back d-flex align-items-center justify-content-center">
                            <span class="fs-4">{{ $flashcard->back }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="button" id="prev-btn" class="btn btn-outline-secondary rounded-pill px-4" style="display: none;">Quay lại</button>
            <button type="button" id="next-btn" class="btn btn-outline-primary rounded-pill px-4">Tiếp theo</button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.flashcard-flip {
    width: 100%;
    max-width: 400px;
    height: 220px;
    perspective: 1000px;
    cursor: pointer;
}
.flashcard-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}
.flashcard-flip.flipped .flashcard-inner {
    transform: rotateY(180deg);
}
.flashcard-front, .flashcard-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    background: #f3f4f6;
    border-radius: 16px;
    box-shadow: 0 1px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    padding: 24px;
}
.flashcard-back {
    background: #e0f2fe;
    transform: rotateY(180deg);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const total = {{ $flashcards->count() }};
    let currentIndex = 0;
    const blocks = document.querySelectorAll('.flashcard-block');
    const progressCurrent = document.getElementById('progress-current');
    const progressBar = document.getElementById('progress-bar');
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');

    function updateProgress() {
        progressCurrent.textContent = currentIndex + 1;
        progressBar.style.width = ((currentIndex + 1) / total * 100) + '%';
        prevBtn.style.display = currentIndex === 0 ? 'none' : 'inline-block';
        nextBtn.textContent = currentIndex === total - 1 ? 'Kết thúc' : 'Tiếp theo';
    }

    function showFlashcard(index) {
        blocks.forEach((block, i) => {
            block.style.display = i === index ? 'block' : 'none';
        });
        updateProgress();
    }

    nextBtn.addEventListener('click', function () {
        if (currentIndex < total - 1) {
            currentIndex++;
            showFlashcard(currentIndex);
        } else {
            // Kết thúc, có thể chuyển hướng hoặc thông báo
            alert('Bạn đã hoàn thành bộ flashcard!');
        }
    });
    prevBtn.addEventListener('click', function () {
        if (currentIndex > 0) {
            currentIndex--;
            showFlashcard(currentIndex);
        }
    });
    showFlashcard(currentIndex);

    // Flip card khi click
    document.querySelectorAll('.flashcard-flip').forEach(card => {
        card.addEventListener('click', function () {
            card.classList.toggle('flipped');
        });
        card.addEventListener('keypress', function (e) {
            if (e.key === ' ' || e.key === 'Enter') {
                card.classList.toggle('flipped');
            }
        });
    });
});
</script>
@endsection 