<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet" />

    <!-- AdminLTE + Bootstrap Icons + Animate -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <style>
        html, body {
            margin: 0;
            padding: 0;
            background-color: #2c1f3b !important;
            height: 100%;
            overflow-x: hidden;
        }
        .content-wrapper, .container-fluid, .content {
            padding: 0 !important;
            margin: 0 !important;
            background-color: #2c1f3b !important;
        }
        .navbar {
            background-color: #2c1f3b !important;
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .navbar-brand {
            color: #fff !important;
            font-weight: 300;
            font-size: 1.5rem;
        }
        .navbar .nav-link {
            color: #fff !important;
            margin-right: 15px;
        }
        .navbar .nav-link:hover {
            color: #e9ecef !important;
        }
    </style>

    @yield('css')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="hold-transition layout-fixed">

@if (Auth::check())
    @php
        $user = auth()->user();
        $quizDoneCount = \App\Models\Result::where('user_id', $user->id)
            ->where('completed_at', '>=', now()->subDays(7))
            ->count();
    @endphp

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('home') }}">QuizMaster</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @if ($user->role === 'quizz_manager')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.quizzes.create') }}">
                                <i class="bi bi-plus-circle me-1"></i> Create content
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#quizzManagerModal">
                                <i class="bi bi-lock-fill me-1"></i> Create content
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('history') }}">
                            <i class="bi bi-clock-history me-1"></i> History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile') }}">
                            <i class="bi bi-person-circle me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- MODAL yêu cầu làm 5 quiz --}}
    <div class="modal fade" id="quizzManagerModal" tabindex="-1" aria-labelledby="quizzManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px; width: 90%;">
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
                    <h5 class="modal-title fw-bold" id="quizzManagerModalLabel">
                        <i class="bi bi-lock-fill me-2"></i> Trở thành Quizz Manager
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light text-dark px-4 py-3">
                    <p class="fs-5"><strong>Bạn chưa đủ điều kiện để tạo nội dung.</strong></p>
                    <p class="mb-2">
                        Để trở thành <span class="fw-bold text-primary">Quizz Manager</span>, bạn cần:
                    </p>
                    <ul class="list-unstyled ps-3">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            Làm ít nhất <strong>5 quiz</strong> 
                        </li>
                        <li>
                            <i class="bi bi-clock-history me-2"></i>
                            Hiện tại bạn đã làm: <strong>{{ $quizDoneCount }}/5</strong> quiz
                        </li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        Sau khi đạt đủ yêu cầu, bạn sẽ được cấp quyền Quizz Manager tự động.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Đóng</button>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill">
                        <i class="bi bi-lightning-fill me-1"></i> Làm quiz ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>
</div>

<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>

@yield('scripts')
</body>
</html>
