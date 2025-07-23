@extends('adminlte::page')

@section('title', 'Chỉnh sửa Khóa học: ' . $course->name)

@section('content_header')
    <h1 class="m-0">Chỉnh sửa Khóa học: {{ $course->name }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid py-4">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <!-- Section 1: Form chỉnh sửa Course -->
    <div class="card card-primary card-outline mb-4">
        <div class="card-header">
            <h3 class="card-title">Chỉnh sửa Khóa học</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="name" class="form-label font-weight-bold">Tên khóa học <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $course->name) }}" required>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="description" class="form-label font-weight-bold">Mô tả</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $course->description) }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="tag_id" class="form-label font-weight-bold">Tag</label>
                    <select name="tag_id" id="tag_id" class="form-control @error('tag_id') is-invalid @enderror">
                        <option value="">-- Chọn Tag --</option>
                        @foreach ($tags ?? [] as $tag)
                            <option value="{{ $tag->id }}" {{ old('tag_id', $course->tag_id) == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    @error('tag_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="image" class="form-label font-weight-bold">Ảnh bìa khóa học</label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if (!empty($course->image))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $course->image) }}" alt="Ảnh bìa" style="max-width: 200px; border-radius: 8px;">
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="slug" class="form-label font-weight-bold">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $course->slug) }}" required>
                    @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save"></i> Cập nhật Khóa học</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Section 2: Danh sách Lesson -->
    <div class="card card-outline card-info mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <h3 class="card-title flex-grow-1">Danh sách Bài học (Lesson)</h3>
            <a href="{{ route('admin.lessons.create', ['course_id' => $course->id]) }}" class="btn btn-success btn-sm ms-auto"><i class="fas fa-plus"></i> Thêm Bài học</a>
        </div>
        <div class="card-body">
            @if (isset($lessons) && $lessons->isEmpty())
                <div class="alert alert-info">Chưa có bài học nào trong khóa học này.</div>
            @elseif (isset($lessons))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tiêu đề</th>
                                <th>Mô tả</th>
                                <th>Thứ tự</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lessons as $index => $lesson)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $lesson->title ?? $lesson->name }}</td>
                                    <td>{{ $lesson->description }}</td>
                                    <td>{{ $lesson->order }}</td>
                                    <td>
                                        <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-warning btn-sm mr-1"><i class="fas fa-edit"></i> Sửa</a>
                                        <a href="{{ route('admin.lessons.flashcards.index', $lesson->id) }}" class="btn btn-info btn-sm mr-1"><i class="fas fa-clone"></i> Flashcard</a>
                                        <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa bài học này?')"><i class="fas fa-trash"></i> Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
   
@endsection

@section('css')
    <style>
        .card { border: none; border-radius: 10px; transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .form-control, .form-check-input { border-radius: 8px; }
        .btn { border-radius: 8px; transition: background-color 0.3s; }
        .table th, .table td { vertical-align: middle; }
        @media (max-width: 576px) {
            .card-tools .input-group { width: 100%; margin-top: 10px; }
            .d-flex.justify-content-end { flex-direction: column; align-items: stretch; }
            .d-flex.justify-content-end .btn { margin-bottom: 10px; }
        }
    </style>
@endsection
