@extends('adminlte::page')

@section('title', 'Quiz Management')

@section('content_header')
    <h1>Quiz Management</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Create New Quiz
            </a>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quizzes List</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Code</th>
                                <th>Is Public</th>
                                <th>Time Limit (minutes)</th>
                                <th style="width: 210px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quizzes as $index => $quiz)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $quiz->name }}</td>
                                    <td>{{ $quiz->description }}</td>
                                    <td>{{ $quiz->code }}</td>
                                    <td>{{ $quiz->is_public ? 'Yes' : 'No' }}</td>
                                    <td>{{ $quiz->time_limit }}</td>
                                    <td>
                                        <a href="{{ route('admin.quizzes.edit', $quiz->id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           data-toggle="tooltip" 
                                           title="Edit Quiz">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" 
                                                    type="submit" 
                                                    data-toggle="tooltip" 
                                                    title="Delete Quiz">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No quizzes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-center">
             
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@stop