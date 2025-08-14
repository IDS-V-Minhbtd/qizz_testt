
@extends('layouts.combined')

@section('content')
<div class="full-width-bg" style="background: linear-gradient(135deg, #1a1030, #2c1f3b); min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <div class="card shadow-lg animate__animated animate__fadeInUp" style="border-radius: 20px; background: linear-gradient(135deg, #6a11cb, #2575fc);">
            <div class="card-body text-white p-4">
                <h3 class="card-title mb-3 text-primary" style="font-family: 'Inter', sans-serif; font-size: 2rem;">
                    🎉 Kết quả bài Quiz: {{ $result->quiz->title }}
                </h3>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-transparent text-white border-light">
                        <strong>✅ Điểm số:</strong> {{ $result->score }}
                    </li>
                    <li class="list-group-item bg-transparent text-white border-light">
                        <strong>⏱️ Thời gian hoàn thành:</strong> 
                        {{ sprintf('%02d:%02d', floor($result->time_taken / 60), $result->time_taken % 60) }}
                    </li>
                </ul>

                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4" style="font-weight: 600; transition: all 0.3s ease;">
                    🔙 Về trang chủ
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

    .list-group-item {
        background: transparent !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: scale(1.03);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .card-title {
            font-size: 1.5rem;
        }
        .btn {
            width: 100%;
        }
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
@endsection