@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
<div class="container mt-4">
    <h2 class="fw-bold mb-4">Add New Course</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Course Title <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="tag_id" class="form-label">Tag</label>
            <select name="tag_id" id="tag_id" class="form-select">
                <option value="">-- Select Tag --</option>
                @foreach ($tags ?? [] as $tag)
                    <option value="{{ $tag->id }}" {{ old('tag_id') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                @endforeach
            </select>
            @error('tag_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Course Image (Upload)</label>
            <input type="file" name="image" id="image" class="form-control">
            @error('image') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug (URL Key) <span class="text-danger">*</span></label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" required>
            @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-success">Create Course</button>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Tự động sinh slug từ title
    document.getElementById('name').addEventListener('input', function () {
        const slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9 -]/g, '')  // xóa ký tự đặc biệt
            .replace(/\s+/g, '-')         // đổi khoảng trắng thành dấu -
            .replace(/-+/g, '-');         // bỏ bớt dấu - liên tục
        document.getElementById('slug').value = slug;
    });
</script>
@endpush
