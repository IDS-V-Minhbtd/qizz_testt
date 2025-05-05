@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ $quiz->name }}</h1>
    <form action="{{ route('quizzes.submit', $quiz->id) }}" method="POST">
        @csrf
        @foreach ($questions as $question)
            <div class="mb-4">
                <h5>{{ $question->question }}</h5>
                @foreach ($question->answers as $answer)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->answer }}" required>
                        <label class="form-check-label">{{ $answer->answer }}</label>
                    </div>
                @endforeach
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Nộp bài</button>
    </form>
</div>
@endsection
