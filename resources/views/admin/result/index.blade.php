@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Kết quả Quiz: {{ $quiz->title }}</h3>
    <p class="text-muted">Hoàn thành lúc: {{ $result->completed_at->format('H:i:s d/m/Y') }}</p>

    <div class="alert alert-success">
        <strong>Điểm:</strong> {{ $result->score }} / {{ $totalQuestions }}
        <br>
        <strong>Thời gian làm bài:</strong> {{ gmdate("i:s", $result->time_taken) }} phút
    </div>

    <h4 class="mt-4">Chi tiết câu trả lời:</h4>
    @foreach($questions as $index => $question)
        @php
            $userAnswer = $userAnswers->firstWhere('question_id', $question->id);
        @endphp
        <div class="card mb-3">
            <div class="card-header">
                Câu {{ $index + 1 }}: {{ $question->question }}
            </div>
            <div class="card-body">
                @foreach($question->answers as $answer)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" disabled
                            {{ $userAnswer && $userAnswer->answer_id == $answer->id ? 'checked' : '' }}>
                        <label class="form-check-label {{ $answer->is_correct ? 'text-success fw-bold' : '' }}">
                            {{ $answer->answer }}
                        </label>
                        @if ($userAnswer && $userAnswer->answer_id == $answer->id && !$answer->is_correct)
                            <span class="text-danger">(Sai)</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Về trang chủ</a>
</div>
@endsection
