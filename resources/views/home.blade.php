@extends('layouts.combined')

@section('content')
<div class="full-width-bg" style="background-color: #2c1f3b; min-height: 100vh; width: 100%; padding: 20px 0;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="mb-5 text-center fw-bold display-5 animate__animated animate__fadeIn" style="color: #ffffff;">
                <form method="GET" action="{{ route('search.quizzes.index') }}" class="mb-4">
                    <div class="input-group shadow" style="max-width: 400px; margin: 0 auto; border-radius: 25px; overflow: hidden;">
                        <input type="text" name="search" class="form-control border-0 py-2" placeholder="Enter your join code here..." value="{{ request('search') }}" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px;" aria-label="Search quiz code">
                        <button class="btn btn-purple fw-bold" type="submit" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px; background-color: #8a4af3; color: #ffffff; border: none;" aria-label="Join quiz">
                            Join Now
                        </button>
                    </div>
                </form>
                <i class="bi bi-book-fill me-2"></i> Danh sách Quiz Công Khai
            </h3>
            @if(auth()->user()?->quizz_manager_until)
                <div class="alert alert-info text-center">
                    Bạn đang dùng quyền <strong>Quizz Manager miễn phí</strong> đến ngày {{ auth()->user()->quizz_manager_until->format('d/m/Y') }}
                </div>
            @endif

            {{-- Hiển thị tối đa 4 catalog, sắp xếp theo popular --}}
            @php
                /**
                 * Sắp xếp catalog theo popular giảm dần, lấy tối đa 4 catalog đầu tiên.
                 *
                 * @param \Illuminate\Support\Collection $catalogs
                 * @return \Illuminate\Support\Collection
                 */
                function getSortedCatalogs($catalogs) {
                    return $catalogs->sortByDesc('popular')->take(4);
                }
                $sortedCatalogs = getSortedCatalogs($catalogs);
            @endphp
            @foreach ($sortedCatalogs as $catalog)
                @if ($catalog->quizzes->isNotEmpty())
                    <div class="mb-5">
                        <h4 class="text-white mb-4 border-bottom pb-2">{{ $catalog->name }} </h4>
                        <div class="quiz-container position-relative">
               
                            <div class="quiz-list d-flex flex-nowrap overflow-auto pb-3">
                               @foreach ($catalog->quizzes->sortByDesc('popular') as $quiz)
                                    <div class="col-md-4 col-sm-6 flex-shrink-0 px-2">
                                        <div class="card quiz-card animate__animated animate__fadeInUp position-relative"
                                             data-aos="fade-up"
                                             style="background: linear-gradient(135deg, #{{ str_pad(dechex(rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT) }}, #{{ str_pad(dechex(rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT) }}); cursor: pointer;"
                                             onclick="openQuizModal({{ $quiz->id }}, '{{ addslashes(Str::limit($quiz->name, 30)) }}', '{{ addslashes(Str::limit($quiz->description, 60)) }}')"
                                             aria-label="Quiz: {{ $quiz->name }}">
                                            <div class="card-body text-white">
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bi bi-puzzle-fill me-3" style="font-size: 2rem;"></i>
                                                    <div>
                                                        <h5 class="card-title mb-1 fw-bold text-truncate" style="max-width: 220px;" title="{{ $quiz->name }}">{{ Str::limit($quiz->name, 50) }}</h5>
                                                        <p class="card-text mb-0 text-truncate" style="font-size: 0.9rem; max-width: 220px;" title="{{ $quiz->description }}">{{ Str::limit($quiz->description, 60) }}</p>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-white text-dark">Code: <b>{{ $quiz->code }}</b></span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-white text-dark">Câu hỏi: <b>{{ $quiz->questions_count ?? $quiz->questions->count() }}</b></span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-white text-dark">Thời gian: <b>{{ $quiz->time_limit }}</b> phút</span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-warning text-dark">Popular: <b>{{ $quiz->popular }}</b></span>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                          
                              <i class="bi bi-arrow-right-circle-fill" style="font-size: 1.5rem;"></i>

                            </button>


                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Thông báo khi không có quiz --}}
            @if ($sortedCatalogs->isEmpty() && $quizzes->isEmpty())
                <div class="alert alert-info text-center animate__animated animate__fadeInUp" role="alert" style="color: #000000; background-color: #e9ecef;">
                    <i class="bi bi-info-circle me-2"></i> Hiện tại chưa có quiz nào được công khai. Hãy thử tạo một quiz mới!
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="quiz-list d-flex flex-nowrap overflow-auto pb-3">
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

    .quiz-container {
        position: relative;
        overflow: hidden;
    }

    .quiz-list {
        margin-top: 20px;
        scrollbar-width: thin;
        scrollbar-color: #8a4af3 #2c1f3b;
    }

    .quiz-list::-webkit-scrollbar {
        height: 8px;
    }

    .quiz-list::-webkit-scrollbar-track {
        background: #2c1f3b;
    }

    .quiz-list::-webkit-scrollbar-thumb {
        background: #8a4af3;
        border-radius: 4px;
    }

    .quiz-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        min-width: 280px;
        max-width: 300px;
    }

    .quiz-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    }

    .scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(138, 74, 243, 0.8);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: background 0.3s ease;
    }

    .scroll-btn:hover {
        background: #8a4af3;
    }

    .scroll-left {
        left: -20px;
    }

    .scroll-right {
        right: -20px;
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

    .modal-header, .modal-footer {
        border: none;
    }

    .modal-footer {
        background: #f8f9fa;
    }

    .btn-outline-secondary, .btn-primary {
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
        transform: translateX(-50%) scale(0.95);
        min-width: 260px;
        max-width: 350px;
        background: rgba(30, 30, 40, 0.98);
        color: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        padding: 18px 20px;
        z-index: 100;
        font-size: 1rem;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .quiz-card:hover .quiz-tooltip {
        opacity: 1;
        transform: translateX(-50%) scale(1);
    }

    @media (max-width: 576px) {
        .quiz-card {
            min-width: 240px;
            text-align: center;
        }

        .quiz-tooltip {
            left: 0;
            right: 0;
            transform: none;
            min-width: 180px;
            max-width: 95vw;
            font-size: 0.95rem;
        }

        .scroll-btn {
            display: none;
        }
    }

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
    AOS.init({
        duration: 800,
        once: true
    });

    function openQuizModal(quizId, quizName, quizDesc) {
        const modalLabel = document.getElementById('quizModalLabel');
        const modalBody = document.getElementById('quizModalBody');
        const startBtn = document.getElementById('startQuizBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');

        const limitedName = quizName.length > 30 ? quizName.substring(0, 30) + '...' : quizName;
        const limitedDesc = quizDesc && quizDesc.length > 60 ? quizDesc.substring(0, 60) + '...' : quizDesc;

        modalLabel.innerText = 'Bắt đầu: ' + limitedName;
        modalBody.innerHTML = (limitedDesc ? `<div class="mb-2" style="color:#444;font-size:1rem;">${limitedDesc}</div>` : '') +
            'Bạn có chắc chắn muốn bắt đầu quiz này?';
        startBtn.href = `/quizz/${quizId}`;

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

    function scrollQuizList(button, distance) {
        const quizList = button.parentElement.querySelector('.quiz-list');
        quizList.scrollBy({ left: distance, behavior: 'smooth' });
    }
</script>
@endsection