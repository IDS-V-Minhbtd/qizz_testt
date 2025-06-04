@extends('adminlte::page')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Tạo Quiz Mới</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
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
            <input type="text" name="name" id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" id="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="4">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="time_limit" class="form-label">Thời gian giới hạn (phút)</label>
            <input type="number" name="time_limit" id="time_limit"
                class="form-control @error('time_limit') is-invalid @enderror"
                value="{{ old('time_limit') }}" min="1">
            @error('time_limit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
    <input type="hidden" name="is_public" value="0"> {{-- Giá trị mặc định nếu không check --}}
    <input type="checkbox" name="is_public" id="is_public"
        class="form-check-input" value="1" {{ old('is_public') ? 'checked' : '' }}>
    <label for="is_public" class="form-check-label">Hiển thị công khai</label>
</div>


        <button type="submit" class="btn btn-success">Tạo Quiz</button>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
