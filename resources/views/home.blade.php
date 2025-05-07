@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Danh sách quiz công khai</h3>

    @if ($quizzes->isEmpty())
        <p>Hiện tại chưa có quiz nào được public.</p>
    @else
        <ul class="list-group">
            @foreach ($quizzes as $quiz)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $quiz->name }}</strong>
                        <br>
                        <small>{{ $quiz->description }}</small>
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="openQuizModal({{ $quiz->id }}, '{{ $quiz->name }}')">Làm quiz</button>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quizModalLabel">Bắt đầu quiz</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="quizModalBody">Bạn có chắc chắn muốn làm quiz này?</p>
            </div>
            <div class="modal-footer">
                <a id="startQuizBtn" href="#" class="btn btn-primary">Bắt đầu</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openQuizModal(quizId, quizName) {
        document.getElementById('quizModalLabel').innerText = 'Bắt đầu: ' + quizName;
        document.getElementById('startQuizBtn').href = `/quizz/${quizId}`;
        const modal = new bootstrap.Modal(document.getElementById('quizModal'));
        modal.show();
    }
</script>
@endsection
