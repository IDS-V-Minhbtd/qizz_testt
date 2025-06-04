<div class="input-group mb-2" data-index="{{ $index }}">
    <input type="text" name="answers[{{ $index }}][text]" class="form-control"
           value="{{ old("answers.$index.text", $answer['text']) }}" placeholder="Đáp án {{ $index + 1 }}" required>
    <input type="hidden" name="answers[{{ $index }}][id]" value="{{ $answer['id'] ?? '' }}">
    <input type="hidden" name="answers[{{ $index }}][is_correct]" value="{{ $answer['is_correct'] }}" class="is-correct-hidden">
    <div class="input-group-text">
        <input type="radio" name="correct_answer" value="{{ $index }}" {{ (int)$correctIndex === $index ? 'checked' : '' }}>
        <span class="ml-2">Đúng</span>
    </div>
</div>
