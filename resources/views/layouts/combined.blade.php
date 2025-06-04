<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet" />

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <!-- Inline CSS to Fix Layout and Style Navbar -->
    <style>
        /* Reset default margins and padding for body and html */
        html, body {
            margin: 0;
            padding: 0;
            background-color: #2c1f3b !important;
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        /* Remove padding and margins from content-wrapper and container-fluid */
        .content-wrapper {
            padding: 0 !important;
            margin: 0 !important;
            background-color: #2c1f3b !important;
            min-height: 100vh;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100%;
        }

        .content {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Navbar styling */
        .navbar {
            background-color: #2c1f3b !important;
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            color: #ffffff !important;
            font-weight: 300;
            font-size: 1.5rem;
        }

        .navbar-brand:hover {
            color: #e9ecef !important;
        }

        .navbar .search-form {
            max-width: 300px;
            margin: 0 20px;
        }

        .navbar .search-form .form-control {
            background-color: #3a2b4f;
            border: none;
            color: #ffffff;
        }

        .navbar .search-form .form-control::placeholder {
            color: #b0b0b0;
        }

        .navbar .search-form .btn {
            background-color: #3a2b4f;
            border: none;
            color: #ffffff;
        }

        .navbar .nav-link {
            color: #ffffff !important;
            margin-right: 15px;
        }

        .navbar .nav-link:hover {
            color: #e9ecef !important;
        }

        /* Ensure navbar items are aligned properly */
        .navbar-nav {
            align-items: center;
        }
    </style>

    <!-- Custom CSS -->
    @yield('css')

    <!-- Vite -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="hold-transition layout-fixed">
    @if (Auth::check())
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('home') }}">QuizMaster</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <form class="search-form d-flex my-2 my-lg-0">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                        <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
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
    @endif

    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    @yield('scripts')
</body>
</html>