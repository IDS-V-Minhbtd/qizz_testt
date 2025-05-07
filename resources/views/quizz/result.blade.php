@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Kết quả bài quiz: {{ $result->quiz->title }}</h2>
    <p>Người làm: {{ $result->user->name ?? 'Ẩn danh' }}</p>
    <p>Tổng số câu hỏi: {{ $result->quiz->questions->count() }}</p>
    <p>Số câu đúng: {{ $result->score }}</p>
    <p>Điểm số: {{ number_format(($result->score / max(1, $result->quiz->questions->count())) * 100, 2) }}%</p>

    <hr>

    <h4>Chi tiết:</h4>
    @foreach ($result->userAnswers as $ua)
        <div class="card mb-3">
            <div class="card-header">
                Câu hỏi: {{ $ua->question->question }}
            </div>
            <div class="card-body">
                <p>Đáp án bạn chọn: <strong>{{ $ua->answer->answer ?? 'Không chọn' }}</strong></p>
                @if ($ua->answer && $ua->answer->is_correct)
                    <p class="text-success">✅ Chính xác</p>
                @else
                    <p class="text-danger">❌ Sai</p>
                    @php
                        $correct = $ua->question->answers->firstWhere('is_correct', true);
                    @endphp
                    <p>Đáp án đúng: <strong>{{ $correct->answer ?? 'Không xác định' }}</strong></p>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
