@extends('layouts.combined')

@section('content')
<div class="full-width-bg" style="background-color: #2c1f3b; min-height: 100vh; width: 100%; padding: 20px 0;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="mb-5 text-center fw-bold display-5 animate__animated animate__fadeIn" style="color: #ffffff;">
                <i class="bi bi-book-fill me-2"></i> Danh sách Quiz Công Khai
            </h3>

            @if ($quizzes->isEmpty())
                <div class="alert alert-info text-center animate__animated animate__fadeInUp" role="alert" style="color: #000000; background-color: #e9ecef;">
                    <i class="bi bi-info-circle me-2"></i> Hiện tại chưa có quiz nào được công khai.
                </div>
            @else
                <div class="row quiz-list">
                    @php
                        $gradients = [
                            'linear-gradient(135deg, #ff4e50, #f9d423)', // Đỏ đậm sang vàng đậm
                            'linear-gradient(135deg, #00c6ff, #0072ff)', // Xanh dương đậm sang xanh dương nhạt
                            'linear-gradient(135deg, #ff7e5f, #feb47b)', // Cam đậm sang cam nhạt
                            'linear-gradient(135deg, #6a11cb, #2575fc)', // Tím đậm sang xanh dương
                            'linear-gradient(135deg, #00e64d, #00b16a)', // Xanh lá đậm sang xanh lá nhạt
                        ];
                    @endphp

                    @foreach ($quizzes as $index => $quiz)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card quiz-card animate__animated animate__fadeInUp" data-aos="fade-up" style="background: {{ $gradients[$index % count($gradients)] }};">
                                <div class="card-body text-white">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-puzzle-fill me-3" style="font-size: 2rem;"></i>
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold">{{ $quiz->name }}</h5>
                                            <p class="card-text mb-0" style="font-size: 0.9rem;">{{ Str::limit($quiz->description, 60) }}</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-white text-dark">Câu hỏi: {{ $quiz->questions_count ?? 'N/A' }}</span>
                                    </div>
                                    <button class="btn btn-white btn-sm w-100 quiz-btn" onclick="openQuizModal({{ $quiz->id }}, '{{ addslashes($quiz->name) }}')">
                                        <i class="bi bi-play-circle me-2 animate__animated animate__pulse animate__infinite"></i> Bắt đầu
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-bold" id="quizModalLabel">Bắt đầu Quiz</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-question-circle-fill text-primary mb-3" style="font-size: 2rem;"></i>
                <p id="quizModalBody">Bạn có chắc chắn muốn bắt đầu quiz này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <a id="startQuizBtn" href="#" class="btn btn-primary px-4">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="loadingSpinner" role="status" aria-hidden="true"></span>
                    Bắt đầu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Full-width background */
    .full-width-bg {
        min-height: 100vh;
        width: 100%;
        padding: 20px 0;
        background-color: #2c1f3b !important;
        color: #ffffff;
    }

    .text-white {
        color: #ffffff !important;
    }

    .quiz-list {
        margin-top: 20px;
    }

    .quiz-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .quiz-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    }

    .quiz-btn {
        border-radius: 25px;
        font-weight: 600;
        background: #ffffff;
        color: #333;
        transition: all 0.3s ease;
    }

    .btn-white {
        background: #ffffff;
        color: #333;
        border: none;
    }

    .quiz-btn:hover {
        background: #e9ecef;
        transform: scale(1.05);
    }

    .modal-content {
        border-radius: 20px;
        overflow: hidden;
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #00c4ff);
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .modal-header {
        border: none;
    }

    .modal-footer {
        border: none;
        background: #f8f9fa;
    }

    .btn-outline-secondary {
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        border-radius: 50px;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .quiz-card {
            text-align: center;
        }
        .quiz-btn {
            margin-top: 10px;
        }
    }

    /* Animation for buttons */
    .animate__pulse {
        animation-duration: 2s;
    }

    /* Adjust alert style for dark theme */
    .alert-info {
        color: #000000;
        background-color: #e9ecef;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        once: true
    });

    function openQuizModal(quizId, quizName) {
        const modalLabel = document.getElementById('quizModalLabel');
        const startBtn = document.getElementById('startQuizBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');

        modalLabel.innerText = 'Bắt đầu: ' + quizName;
        startBtn.href = `/quizz/${quizId}`;

        // Show loading spinner on click
        startBtn.onclick = function() {
            loadingSpinner.classList.remove('d-none');
            startBtn.classList.add('disabled');
            setTimeout(() => {
                window.location.href = startBtn.href;
            }, 500);
        };

        const modal = new bootstrap.Modal(document.getElementById('quizModal'));
        modal.show();
    }
</script>
@endsection