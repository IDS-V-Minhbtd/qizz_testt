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
                    <strong>⏱️ Thời gian hoàn thành:</strong> {{ gmdate("i:s", $result->time_taken) }}
                </li>
                <li class="list-group-item">
                    <strong>🕒 Thời gian bắt đầu:</strong> {{ $result->start_time ?? 'N/A' }}
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
