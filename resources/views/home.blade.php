@extends('layouts.combined')

@section('content')
<div class="full-width-bg" style="background-color: #2c1f3b; min-height: 100vh; width: 100%; padding: 20px 0;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="mb-5 text-center fw-bold display-5 animate__animated animate__fadeIn" style="color: #ffffff;">
                <form method="GET" action="{{ route('search.quizzes.index') }}" class="mb-4">
                    <div class="input-group shadow" style="max-width: 400px; margin: 0 auto; border-radius: 25px; overflow: hidden;">
                        <input type="text" name="search" class="form-control border-0 py-2" placeholder="Enter your join code here..." value="{{ request('search') }}" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;">
                        <button class="btn btn-purple fw-bold" type="submit" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px; background-color: #8a4af3; color: #ffffff; border: none;">
                            Join Now
                        </button>
                    </div>
                </form>
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
                            <div class="card quiz-card animate__animated animate__fadeInUp position-relative"
                                 data-aos="fade-up"
                                 style="background: {{ $gradients[$index % count($gradients)] }}; cursor:pointer;"
                                 onclick="openQuizModal({{ $quiz->id }}, '{{ addslashes(Str::limit($quiz->name, 30)) }}', '{{ addslashes(Str::limit($quiz->description, 60)) }}')">
                                <div class="card-body text-white">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-puzzle-fill me-3" style="font-size: 2rem;"></i>
                                        <div>
                                            <h5 class="card-title mb-1 fw-bold text-truncate" style="max-width: 220px;">{{ Str::limit($quiz->name, 50) }}</h5>
                                            <p class="card-text mb-0 text-truncate" style="font-size: 0.9rem; max-width: 220px;">{{ Str::limit($quiz->description, 60) }}</p>
                                        </div>
                                    </div>
                                    <div clas="mb-3">
                                        <span class="badge bg-white text-dark">{{ $quiz->code }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-white text-dark">Câu hỏi: {{ $quiz->questions_count ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="badge bg-white text-dark">Thời gian: {{ $quiz->time_limit }} phút</span>
                                    </div>
                                </div>
                                <!-- Tooltip hiển thị toàn bộ thông tin quiz -->
                                <div class="quiz-tooltip" style="display:none;">
                                    <div style="font-weight:bold; font-size:1.1rem;">{{ $quiz->name }}</div>
                                    <div style="margin-bottom:6px;">{{ $quiz->description }}</div>
                                    <div><b>Mã:</b> {{ $quiz->code }}</div>
                                    <div><b>Số câu hỏi:</b> {{ $quiz->questions_count ?? 'N/A' }}</div>
                                    <div><b>Thời gian:</b> {{ $quiz->time_limit }} phút</div>
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
        position: relative;
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

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .quiz-tooltip {
        position: absolute;
        left: 50%;
        top: 10px;
        transform: translateX(-50%);
        min-width: 260px;
        max-width: 350px;
        background: rgba(30,30,40,0.98);
        color: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        padding: 18px 20px;
        z-index: 100;
        font-size: 1rem;
        pointer-events: none;
        white-space: pre-line;
        opacity: 0;
        transition: opacity 0.18s;
        word-break: break-word;
    }

    .quiz-card:hover .quiz-tooltip {
        display: block;
        opacity: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .quiz-card {
            text-align: center;
        }
        .quiz-btn {
            margin-top: 10px;
        }
        .quiz-tooltip {
            left: 0;
            right: 0;
            transform: none;
            min-width: 180px;
            max-width: 95vw;
            font-size: 0.95rem;
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

    function openQuizModal(quizId, quizName, quizDesc) {
        const modalLabel = document.getElementById('quizModalLabel');
        const modalBody = document.getElementById('quizModalBody');
        const startBtn = document.getElementById('startQuizBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');

        // Giới hạn ký tự tên quiz trong popup (30 ký tự)
        const limitedName = quizName.length > 30 ? quizName.substring(0, 30) + '...' : quizName;
        // Giới hạn mô tả (60 ký tự)
        const limitedDesc = quizDesc && quizDesc.length > 60 ? quizDesc.substring(0, 60) + '...' : quizDesc;

        modalLabel.innerText = 'Bắt đầu: ' + limitedName;
        modalBody.innerHTML = (limitedDesc ? `<div class="mb-2" style="color:#444;font-size:1rem;">${limitedDesc}</div>` : '') +
            'Bạn có chắc chắn muốn bắt đầu quiz này?';
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