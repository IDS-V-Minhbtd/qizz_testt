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
                    <table class="table table-bordered table-striped mb-0" style="table-layout: fixed; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 18%;">Name</th>
                                <th style="width: 32%;">Description</th>
                                <th style="width: 12%;">Code</th>
                                <th style="width: 10%;">Is Public</th>
                                <th style="width: 13%;">Time Limit (minutes)</th>
                                <th style="width: 210px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quizzes as $index => $quiz)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-truncate" style="max-width: 180px;">{{ Str::limit($quiz->name, 24) }}</td>
                                    <td class="text-truncate" style="max-width: 320px;">{{ Str::limit($quiz->description, 200) }}</td>
                                    <td class="text-truncate" style="max-width: 120px;">{{ $quiz->code }}</td>
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

@section('css')
    <style>
        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .form-control, .form-check-input {
            border-radius: 8px;
        }
        .btn {
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .table th, .table td {
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        @media (max-width: 576px) {
            .card-tools .input-group {
                width: 100%;
                margin-top: 10px;
            }
            .d-flex.justify-content-end {
                flex-direction: column;
                align-items: stretch;
            }
            .d-flex.justify-content-end .btn {
                margin-bottom: 10px;
            }
        }
    </style>
@endsection