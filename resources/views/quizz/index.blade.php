@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Bắt đầu Quiz: {{ $quiz->title }}</h3>
    <p>{{ $quiz->description }}</p>

    <div id="quiz-container">
        @foreach($questions as $index => $question)
            <div class="card mb-3 question-block" data-index="{{ $index }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                <div class="card-header">Câu {{ $index + 1 }} trên {{ $questions->count() }}</div>
                <div class="card-body">
                    <p class="fw-bold">{{ $question->question }}</p>
                    @foreach($question->answers as $answer)
                        <button class="btn btn-outline-primary d-block mb-2 answer-btn"
                            data-answer-id="{{ $answer->id }}"
                            data-question-id="{{ $question->id }}">
                            {{ $answer->answer }}
                        </button>
                    @endforeach
                    <div class="result mt-2 fw-bold"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="summary" class="mt-4" style="display:none;">
        <h4>Kết quả</h4>
        <p>Bạn đã trả lời đúng <span id="correct-count">0</span> trên {{ $questions->count() }} câu.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const totalQuestions = {{ $questions->count() }};
    let currentIndex = 0;
    let correctCount = 0;

    const showQuestion = index => {
        document.querySelectorAll('.question-block').forEach(block => {
            block.style.display = block.dataset.index == index ? '' : 'none';
        });
    };

    const nextOrFinish = () => {
        if (currentIndex + 1 < totalQuestions) {
            currentIndex++;
            showQuestion(currentIndex);
        } else {
            document.getElementById('quiz-container').style.display = 'none';
            document.getElementById('correct-count').textContent = correctCount;
            document.getElementById('summary').style.display = '';
        }
    };

    document.querySelectorAll('.answer-btn').forEach(button => {
        button.addEventListener('click', function () {
            const answerId = this.dataset.answerId;
            const questionId = this.dataset.questionId;
            const container = this.closest('.question-block');
            const resultEl = container.querySelector('.result');

            container.querySelectorAll('.answer-btn').forEach(btn => btn.disabled = true);

            fetch('/api/check-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ answer_id: answerId, question_id: questionId })
            })
            .then(res => res.json())
            .then(data => {
                resultEl.textContent = data.message;
                resultEl.classList.add(data.correct ? 'text-success' : 'text-danger');
                if (data.correct) correctCount++;
                setTimeout(nextOrFinish, 1000);
            })
            .catch(() => {
                resultEl.textContent = 'Lỗi xảy ra!';
                resultEl.classList.add('text-danger');
                setTimeout(nextOrFinish, 1000);
            });
        });
    });
});
</script>
@endsection
