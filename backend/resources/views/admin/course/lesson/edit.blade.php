@extends('adminlte::page')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Chỉnh sửa Bài học</h2>
    <form action="{{ route('admin.lesson.update', $lesson->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="course_id" class="form-label">Khóa học</label>
            <select name="course_id" id="course_id" class="form-select" required>
                <option value="">-- Chọn khóa học --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @if($lesson->course_id == $course->id) selected @endif>{{ $course->title ?? $course->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $lesson->title }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ $lesson->description }}</textarea>
        </div>
        <div class="mb-3">
            <label for="video_url" class="form-label">Video URL</label>
            <input type="url" name="video_url" id="video_url" class="form-control" value="{{ $lesson->video_url }}">
        </div>
        <div class="mb-3">
            <label for="order" class="form-label">Thứ tự</label>
            <input type="number" name="order" id="order" class="form-control" min="1" value="{{ $lesson->order }}">
        </div>
        <button type="submit" class="btn btn-success rounded-pill px-4">Cập nhật</button>
        <a href="{{ route('admin.lesson.index') }}" class="btn btn-secondary rounded-pill px-4 ms-2">Hủy</a>
    </form>
</div>
@endsection
