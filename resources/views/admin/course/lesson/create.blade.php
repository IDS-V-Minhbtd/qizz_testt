@extends('adminlte::page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeIn">
                <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex align-items-center">
                    <i class="fas fa-book-open fa-lg me-2"></i>
                    <h3 class="mb-0 flex-grow-1">Thêm Bài học mới</h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $courseId }}">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg @error('title') is-invalid @enderror" required placeholder="Nhập tiêu đề bài học...">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">Nội dung bài học</label>
                            <div class="input-group mb-2">
                                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="7" placeholder="Nhập hoặc import nội dung bài học..."></textarea>
                                <button type="button" class="btn btn-outline-primary" id="import-btn">
                                    <i class="fas fa-file-import"></i> Import từ file
                                </button>
                            </div>
                            <input type="file" id="file-input" accept=".docx,.pdf" style="display:none">
                            <small class="text-muted">Chấp nhận file Word (.docx) hoặc PDF. Nội dung sẽ tự động điền vào ô bên trên.</small>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="youtube_url" class="form-label fw-bold">Youtube URL</label>
                            <input type="url" name="youtube_url" id="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror" placeholder="https://youtube.com/...">
                            @error('youtube_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="order_index" class="form-label fw-bold">Thứ tự</label>
                            <input type="number" name="order_index" id="order_index" class="form-control @error('order_index') is-invalid @enderror" min="1" value="1">
                            @error('order_index') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success rounded-pill px-4 me-2 animate__animated animate__pulse animate__infinite">Lưu</button>
                            <a href="{{ route('admin.courses.edit', $courseId) }}" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Mammoth.js for docx, PDF.js for PDF (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.7.0/mammoth.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.getElementById('import-btn').addEventListener('click', function() {
    document.getElementById('file-input').click();
});

document.getElementById('file-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext === 'docx') {
        const reader = new FileReader();
        reader.onload = function(event) {
            mammoth.convertToHtml({arrayBuffer: event.target.result})
                .then(function(result) {
                    // Chuyển HTML sang text thuần
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = result.value;
                    document.getElementById('content').value = tempDiv.innerText.trim();
                });
        };
        reader.readAsArrayBuffer(file);
    } else if (ext === 'pdf') {
        const reader = new FileReader();
        reader.onload = function(event) {
            const typedarray = new Uint8Array(event.target.result);
            pdfjsLib.getDocument({data: typedarray}).promise.then(function(pdf) {
                let text = '';
                let loadPage = function(pageNum) {
                    pdf.getPage(pageNum).then(function(page) {
                        page.getTextContent().then(function(content) {
                            content.items.forEach(function(item) {
                                text += item.str + ' ';
                            });
                            if (pageNum < pdf.numPages) {
                                loadPage(pageNum + 1);
                            } else {
                                document.getElementById('content').value = text.trim();
                            }
                        });
                    });
                };
                loadPage(1);
            });
        };
        reader.readAsArrayBuffer(file);
    } else {
        alert('Chỉ hỗ trợ file .docx hoặc .pdf');
    }
});
</script>
@endsection
