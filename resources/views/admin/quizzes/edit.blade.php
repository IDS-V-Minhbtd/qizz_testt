@extends('adminlte::page')

@section('title', 'Chỉnh sửa Quiz: ' . $quiz->name)

@section('content_header')
    <h1 class="m-0">Chỉnh sửa Quiz: {{ $quiz->name }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>
@endsection
 
@section('content')
<div class="container-fluid py-4">
    <!-- Thông báo lỗi -->
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

    <!-- Section 1: Form chỉnh sửa Quiz -->
    <div class="card card-primary card-outline mb-4">
        <div class="card-header">
            <h3 class="card-title">Chỉnh sửa Quiz</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="quiz-form" action="{{ route('admin.quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Tên quiz -->
                <div class="form-group mb-3">
                    <label for="name" class="form-label font-weight-bold">Tên Quiz <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $quiz->name) }}" >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mô tả quiz -->
                <div class="form-group mb-3">
                    <label for="description" class="form-label font-weight-bold">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $quiz->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Giới hạn thời gian -->
                <div class="form-group mb-3">
                    <label for="time_limit" class="form-label font-weight-bold">Giới hạn thời gian (phút) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit', ($quiz->time_limit ?? 10)) }}"  min="1">
                    @error('time_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Công khai -->
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_public" name="is_public" value="1" {{ old('is_public', $quiz->is_public) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_public">Công khai</label>
                </div>

                <!-- Nút hành động -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2" id="submit-btn">
                        <i class="fas fa-save"></i> Cập nhật Quiz
                    </button>
                    <a href="{{ route('admin.quizzes.questions.create', $quiz->id) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm câu hỏi
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Section 2: Danh sách câu hỏi -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Danh sách câu hỏi</h3>
            <div class="card-tools">
                <!-- Tìm kiếm -->
                <div class="input-group input-group-sm" style="width: 200px;">
                    <input type="text" id="search-questions" class="form-control" placeholder="Tìm kiếm câu hỏi...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if (isset($questions) && $questions->isEmpty())
                <div class="alert alert-info">Chưa có câu hỏi nào được thêm.</div>
            @elseif (isset($questions))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Câu hỏi</th>
                                <th>Loại đáp án</th>
                                <th>Thứ tự</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="questions-table">
                            @foreach ($questions as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ Str::limit($question->question, 50) }}</td>
                                    <td>{{ $question->answer_type }}</td>
                                    <td>{{ $question->order }}</td>
                                    <td>
                                        <a href="{{ route('admin.quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="btn btn-warning btn-sm mr-1">
                                            <i class="fas fa-edit"></i> Sửa 
                                        </a>
                                        <form action="{{ route('admin.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Phân trang -->
                <div class="mt-3">
                   {{ $questions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

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

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tìm kiếm câu hỏi
            const searchInput = document.getElementById('search-questions');
            const questionsTable = document.getElementById('questions-table');

            searchInput.addEventListener('input', function () {
                const searchText = this.value.toLowerCase();
                const rows = questionsTable.getElementsByTagName('tr');

                Array.from(rows).forEach(row => {
                    const questionText = row.cells[1].textContent.toLowerCase();
                    row.style.display = questionText.includes(searchText) ? '' : 'none';
                });
            });

            // Xác nhận xóa câu hỏi
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (!confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
