@extends('adminlte::page')

@section('title', 'User Results')

@section('content_header')
    <h1 class="text-center">Results of {{ $user->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
        </div>
        <div class="card-body">
            @if($results->isEmpty())
                <div class="alert alert-info text-center">
                    No quiz results found.
                </div>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quiz Title</th>
                            <th>Score</th>
                            <th>Date Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $result->quiz->name ?? 'N/A' }}</td>
                                <td>{{ $result->score }}</td>
                                <td>{{ $result->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop
