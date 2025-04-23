@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    
                    <div class="mt-4">
                        <h2>Welcome to the Quiz Application</h2>
                        <p>Here you can take fun quizzes.</p>
                        <a href="{{ route('quizzes.index') }}" class="btn btn-primary">View Quizzes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
