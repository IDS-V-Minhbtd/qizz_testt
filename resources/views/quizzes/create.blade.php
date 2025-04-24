@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Tạo Quiz mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.quizzes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên Quiz</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="time_limit" class="form-label">Giới hạn thời gian (phút)</label>
            <input type="number" name="time_limit" class="form-control" id="time_limit" value="{{ old('time_limit', 10) }}" required>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_public" class="form-check-input" id="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_public">Công khai</label>
        </div>

        <button type="submit" class="btn btn-primary">Tạo Quiz</button>
    </form>
</div>

@endsection
