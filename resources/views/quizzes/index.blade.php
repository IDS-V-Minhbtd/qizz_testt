@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Quizzes</h1>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary mb-3">Create New Quiz</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quizzes as $quiz)
            <tr>
                <td>{{ $quiz->name }}</td>
                <td>{{ $quiz->description }}</td>
                <td>
                    <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
