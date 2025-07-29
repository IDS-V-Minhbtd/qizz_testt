@extends('adminlte::page')

@section('title', 'Course Management')

@section('content_header')
    <h1>Course Management</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.courses.create') }}" class="btn btn-success mb-3">
                <i class="fas fa-plus"></i> Create New Course
            </a>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Courses List</h3>
                </div>
                <div class="card-body p-0">
                    @if (session('success'))
                        <div class="alert alert-success m-3">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered table-striped mb-0" style="table-layout: fixed; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 25%;">Description</th>
                                <th style="width: 20%;">Tag</th>
                                <th style="width: 15%;">Lessons</th>
                                <th style="width: 210px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($courses as $index => $course)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-truncate" style="max-width: 250px;">{{ Str::limit($course->name, 30) }}</td>
                                    <td class="text-truncate" style="max-width: 250px;">{{ Str::limit($course->description, 30) }}</td>
                                    <td class="text-truncate" style="max-width: 200px;">{{ Str::limit($course->tag->name ?? '-', 20) }}</td>
                                    <td>{{ $course->lessons->count() }}</td>
                                    <td>
                                        <a href="{{ route('admin.courses.show', $course->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           data-toggle="tooltip" 
                                           title="View Course">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course->id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           data-toggle="tooltip" 
                                           title="Edit Course">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.courses.destroy', $course->id) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this course?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" 
                                                    type="submit" 
                                                    data-toggle="tooltip" 
                                                    title="Delete Course">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No courses found.</td>
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
@stop

@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop