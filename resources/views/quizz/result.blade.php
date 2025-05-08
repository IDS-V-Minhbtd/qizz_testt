@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Kết quả bài Quiz: {{ $result->quiz->title }}</h3>
    <p>Điểm số: <strong>{{ $result->score }}</strong></p>
    <p>Thời gian hoàn thành: {{ gmdate("i:s", $result->time_taken) }}</p>
    <p>Hoàn thành lúc: {{ $result->completed_at->format('H:i:s d/m/Y') }}</p>

    <hr>
    <h5>Câu trả lời của bạn:</h5>
    <ul>
        @foreach($result->userAnswers as $userAnswer)
            <li>
                <strong>Câu hỏi:</strong> {{ $userAnswer->question->question }} <br>
                <strong>Trả lời:</strong> {{ $userAnswer->answer->answer }} -
                <span class="{{ $userAnswer->is_correct ? 'text-success' : 'text-danger' }}">
                    {{ $userAnswer->is_correct ? 'Đúng' : 'Sai' }}
                </span>
            </li>
        @endforeach
    </ul>
</div>
@endsection
