@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title mb-3 text-primary">🎉 Kết quả bài Quiz: {{ $result->quiz->title }}</h3>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item">
                    <strong>✅ Điểm số:</strong> {{ $result->score }}
                </li>
                <li class="list-group-item">
                    <strong>⏱️ Thời gian hoàn thành:</strong> 
                    {{ sprintf('%02d:%02d', floor($result->time_taken / 60), $result->time_taken % 60) }}
                </li>
            </ul>

            </div>

            <a href="{{ route('home') }}" class="btn btn-primary">
                🔙 Về trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
