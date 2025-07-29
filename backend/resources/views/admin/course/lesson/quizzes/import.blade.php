@extends('adminlte::page')

@section('title', 'Import Questions')

@section('content')
<div class="container py-4" style="background-color: #f9f9f9; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow rounded-4" data-aos="fade-up">
                <div class="card-body p-4">
                    <h4 class="mb-4 fw-bold text-primary">
                        <i class="bi bi-upload me-2"></i>Import Questions
                    </h4>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-4" id="importTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-document" data-bs-toggle="tab" data-bs-target="#document" type="button" role="tab">
                                <i class="bi bi-file-earmark-arrow-up me-1"></i> Document
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-paste" data-bs-toggle="tab" data-bs-target="#paste" type="button" role="tab">
                                <i class="bi bi-clipboard-data me-1"></i> Paste Questions
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Upload file -->
                        <div class="tab-pane fade show active" id="document" role="tabpanel" aria-labelledby="tab-document">
                            <form action="{{ route('admin.questions.import.file', $quiz->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="p-4 border rounded-4 text-center bg-white" style="border-style: dashed;" data-aos="zoom-in">
                                    <p class="fs-5 fw-semibold mb-3">Upload from your device</p>
                                    <div class="mb-3">
                                        <input type="file" name="import_file" id="import_file" class="form-control d-none" onchange="this.form.submit()">
                                        <button type="button" onclick="document.getElementById('import_file').click()" class="btn btn-primary rounded-pill px-4 py-2">
                                            <i class="bi bi-upload me-2"></i>Choose File
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Supported: .txt, .cvs Max:10mb
                                    </small>
                                </div>
                            </form>

                            @if(session('uploaded_file'))
                                <div class="mt-4 text-start bg-white p-4 rounded-4 shadow-sm">
                                    <h6 class="fw-bold">Uploaded File:</h6>
                                    <p class="mb-2">
                                        <i class="bi bi-file-earmark-check-fill me-1 text-success"></i>
                                        {{ session('uploaded_file')['original_name'] }} ({{ session('uploaded_file')['size'] }} KB)
                                    </p>

                                    @if(session('uploaded_file')['is_previewable'])
                                        <iframe src="{{ session('uploaded_file')['url'] }}" width="100%" height="400px" class="border rounded-3"></iframe>
                                    @else
                                        <a href="{{ session('uploaded_file')['url'] }}" class="btn btn-sm btn-outline-primary" target="_blank">Download / View</a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Paste content -->
                        <div class="tab-pane fade" id="paste" role="tabpanel" aria-labelledby="tab-paste">
                            <form method="POST" action="{{ route('admin.questions.import.text', $quiz->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="questions_text" class="form-label fw-bold">
                                        Paste your questions here:
                                    </label>
                                    <textarea name="questions_text" id="questions_text" class="form-control shadow-sm" rows="10" placeholder="1. What is the capital of France?&#10;A. Paris&#10;B. London&#10;C. Berlin&#10;D. Madrid&#10;Answer: A"></textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Format:
                                        <code class="d-block mt-1">
                                            1. Question text?<br>
                                            A. Option 1<br>
                                            B. Option 2<br>
                                            C. Option 3<br>
                                            D. Option 4<br>
                                            Answer: A
                                        </code>
                                    </small>
                                    <button type="submit" class="btn btn-success rounded-pill px-4">
                                        <i class="bi bi-check-circle me-1"></i> Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div>
    </div>
</div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
@endpush
