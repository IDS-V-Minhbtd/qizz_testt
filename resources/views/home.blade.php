@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="mb-4 text-primary fw-bold">Danh sách Quiz Công Khai</h3>

            @if ($quizzes->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i> Hiện tại chưa có quiz nào được công khai.
                </div>
            @else
                <div class="quiz-list">
                    @foreach ($quizzes as $quiz)
                        <div class="card mb-3 shadow-sm quiz-card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1 text-dark">{{ $quiz->name }}</h5>
                                    <p class="card-text text-muted mb-0">{{ $quiz->description }}</p>
                                </div>
                                <button class="btn btn-primary btn-sm px-4 quiz-btn" onclick="openQuizModal({{ $quiz->id }}, '{{ addslashes($quiz->name) }}')">
                                    <i class="bi bi-play-circle me-2"></i> Làm Quiz
                                </button>
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="quizModalLabel">Bắt đầu Quiz</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="quizModalBody" class="text-center">Bạn có chắc chắn muốn bắt đầu quiz này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <a id="startQuizBtn" href="#" class="btn btn-primary px-4">Bắt đầu</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .quiz-list {
        margin-top: 20px;
    }

    .quiz-card {
        border: none;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .quiz-btn {
        border-radius: 20px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .quiz-btn:hover {
        background-color: #0056b3;
    }

    .modal-content {
        border-radius: 15px;
    }

    .modal-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .modal-footer {
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .btn-outline-secondary {
        border-radius: 20px;
    }

    .btn-primary {
        border-radius: 20px;
    }
</style>
@endsection

@section('scripts')
<script>
    function openQuizModal(quizId, quizName) {
        document.getElementById('quizModalLabel').innerText = 'Bắt đầu: ' + quizName;
        document.getElementById('startQuizBtn').href = `/quizz/${quizId}`;
        const modal = new bootstrap.Modal(document.getElementById('quizModal'));
        modal.show();
    }
</script>
@endsection