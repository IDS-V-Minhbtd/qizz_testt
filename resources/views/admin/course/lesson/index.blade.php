@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Quản lý Bài học (Lesson)</h2>
        <a href="{{ route('admin.lesson.create') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Thêm Bài học
        </a>
    </div>
    <table class="table table-bordered table-hover bg-white rounded-3">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Khóa học</th>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Thứ tự</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lessons as $lesson)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $lesson->course->title ?? $lesson->course->name }}</td>
                <td>{{ $lesson->title ?? $lesson->name }}</td>
                <td>{{ $lesson->description }}</td>
                <td>{{ $lesson->order }}</td>
                <td>
                    <a href="{{ route('admin.lesson.edit', $lesson->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.lesson.destroy', $lesson->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
