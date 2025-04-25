@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Làm bài Quiz</h3>

    {{-- Điều hướng câu hỏi --}}
    <div class="mb-3" id="question-nav">
        @php
            // Gán câu hỏi vào biến trực tiếp trong view
            $questions = [
                (object)['question' => '1 + 1 = ?'],
                (object)['question' => 'Thủ đô của Việt Nam là?'],
                (object)['question' => 'Laravel là framework của ngôn ngữ nào?']
            ];
        @endphp

        @foreach ($questions as $index => $question)
            <button class="btn btn-sm btn-outline-primary m-1" onclick="showQuestion({{ $index }})" id="btn-q{{ $index }}">
                {{ $index + 1 }}
            </button>
        @endforeach
    </div>

    {{-- Hiển thị câu hỏi --}}
    <div class="card">
        <div class="card-body">
            <div id="question-content">
                {{-- Nội dung câu hỏi sẽ render bằng JS --}}
            </div>
        </div>
    </div>

</div>

<script>
    // Đảm bảo biến questions được khai báo trong JavaScript
    const questions = @json($questions);  // Chuyển biến PHP sang JSON
    let currentIndex = 0;

    function renderQuestion() {
        const content = document.getElementById('question-content');
        content.innerHTML = `
            <h5>Câu hỏi ${currentIndex + 1}</h5>
            <p>${questions[currentIndex].question}</p>
        `;

        questions.forEach((_, i) => {
            const btn = document.getElementById(`btn-q${i}`);
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });

        const currentBtn = document.getElementById(`btn-q${currentIndex}`);
        currentBtn.classList.remove('btn-outline-primary');
        currentBtn.classList.add('btn-primary');
    }

    function showQuestion(index) {
        currentIndex = index;
        renderQuestion();
    }

    // Hiển thị câu hỏi đầu tiên khi trang tải xong
    document.addEventListener("DOMContentLoaded", renderQuestion);
</script>
@endsection
