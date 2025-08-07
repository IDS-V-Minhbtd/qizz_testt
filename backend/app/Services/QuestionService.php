<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\AnswerRepositoryInterface;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;

class QuestionService
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepo,
        protected QuizRepositoryInterface $quizRepo,
        protected AnswerRepositoryInterface $answerRepo
    ) {}

    public function paginateByQuizId(int $quizId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->questionRepo->paginateByQuizId($quizId, $perPage);
    }

    public function getById(int $id)
    {
        return $this->questionRepo->findById($id);
    }

    public function delete(int $id)
    {
        return $this->questionRepo->delete($id);
    }

    public function create(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            $this->ensureUniqueOrder($data['quiz_id'], $data['order']);

            if (request()->hasFile('image')) {
                $data['image'] = request()->file('image')->store('questions', 'public');
            }

            // Sử dụng trực tiếp $data từ Request
            $question = $this->questionRepo->create($data);
            $this->createAnswersForQuestion($question->id, $data);

            return $this->questionRepo->findById($question->id);
        });
    }

    public function update(int $questionId, array $data): Question
    {
        return DB::transaction(function () use ($questionId, $data) {
            if (request()->hasFile('image')) {
                $data['image'] = request()->file('image')->store('questions', 'public');
            }

            $this->updateQuestion($questionId, $data);
            $this->updateAnswersForQuestion($questionId, $data);

            return $this->questionRepo->findById($questionId);
        });
    }

    public function importText($quizId)
    {
        $quiz = $this->quizRepo->findById($quizId);
        if (!$quiz) {
            abort(404, 'Quiz không tồn tại');
        }

        $questionsText = request('questions_text');
        Log::info('Nội dung paste import:', ['content' => $questionsText]);

        // Tách thành các block câu hỏi (2 dòng trống trở lên)
        $blocks = preg_split('/\R{2,}/', trim($questionsText));
        $order = 1;
        $imported = 0;

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $block))));
            Log::info('Block đang xử lý (paste):', ['block' => $block, 'lines' => $lines]);

            if (empty($lines)) continue;

            $questionText = array_shift($lines);
            $answers = [];
            $correct = null;

            foreach (['A', 'B', 'C', 'D'] as $idx => $label) {
                if (!isset($lines[$idx])) continue;
                $ansText = preg_replace('/^[A-D]\.\s*/', '', $lines[$idx]);
                $answers[$label] = $ansText;
            }

            foreach ($lines as $line) {
                if (preg_match('/^(?:Đáp án|Answer)\s*[:\-]?\s*([A-D])$/iu', $line, $m)) {
                    $correct = strtoupper($m[1]);
                    break;
                }
            }

            Log::info('Dữ liệu parse được (paste):', [
                'question' => $questionText,
                'answers' => $answers,
                'correct' => $correct,
            ]);

            if (!$questionText || count($answers) < 2 || !$correct || !isset($answers[$correct])) {
                Log::warning('Dữ liệu không hợp lệ, bỏ qua block', ['block' => $block]);
                continue;
            }

            try {
                // Sử dụng trực tiếp dữ liệu đã chuẩn hóa
                $question = $this->questionRepo->create([
                    'quiz_id' => $quizId,
                    'question' => $questionText,
                    'order' => $order++,
                    'type' => 'multiple_choice',
                ]);
                $answersForService = [];
                foreach ($answers as $label => $ansText) {
                    $answersForService[$label] = ['text' => $ansText];
                }
                $this->createAnswersForQuestion($question->id, [
                    'answer_type' => 'multiple_choice',
                    'answers' => $answersForService,
                    'correct_answer' => $correct,
                ]);
                $imported++;
                Log::info('Đã lưu câu hỏi thành công (paste)', ['question_id' => $question->id]);
            } catch (Exception $e) {
                Log::error('Lỗi khi lưu câu hỏi từ paste import', [
                    'error' => $e->getMessage(),
                    'block' => $block
                ]);
                continue;
            }
        }

        Log::info("Import paste hoàn tất. Tổng số câu hỏi import: {$imported}");

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', "Đã import thành công {$imported} câu hỏi từ paste.");
    }

    public function importFile(Request $request, $quizId)
    {
        $quiz = $this->quizRepo->findById($quizId);
        if (!$quiz) {
            abort(404, 'Quiz không tồn tại');
        }

        $request->validate([
            'import_file' => 'required|file|max:25600', // 25MB
        ]);

        $file = $request->file('import_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['txt', 'csv'])) {
            return redirect()->back()->withErrors(['import_file' => 'Chỉ hỗ trợ file .txt hoặc .csv']);
        }

        $content = file_get_contents($file->getRealPath());

        Log::info('Nội dung file import:', ['content' => $content]);

        // Tách thành các block câu hỏi
        $blocks = preg_split('/\R{2,}/', trim($content));
        $order = 1;
        $imported = 0;

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $block))));
            Log::info('Block đang xử lý:', ['block' => $block, 'lines' => $lines]);

            if (empty($lines)) continue;

            $questionText = array_shift($lines);
            $answers = [];
            $correct = null;

            foreach (['A', 'B', 'C', 'D'] as $idx => $label) {
                if (!isset($lines[$idx])) continue;
                $ansText = preg_replace('/^[A-D]\.\s*/', '', $lines[$idx]);
                $answers[$label] = $ansText;
            }

            foreach ($lines as $line) {
                if (preg_match('/^(?:Đáp án|Answer)\s*[:\-]?\s*([A-D])$/iu', $line, $m)) {
                    $correct = strtoupper($m[1]);
                    break;
                }
            }

            Log::info('Dữ liệu parse được:', [
                'question' => $questionText,
                'answers' => $answers,
                'correct' => $correct,
            ]);

            if (!$questionText || count($answers) < 2 || !$correct || !isset($answers[$correct])) {
                Log::warning('Dữ liệu không hợp lệ, bỏ qua block', ['block' => $block]);
                continue;
            }

            try {
                // Sử dụng trực tiếp dữ liệu đã chuẩn hóa
                $question = $this->questionRepo->create([
                    'quiz_id' => $quizId,
                    'question' => $questionText,
                    'order' => $order++,
                    'type' => 'multiple_choice',
                ]);
                $answersForService = [];
                foreach ($answers as $label => $ansText) {
                    $answersForService[$label] = ['text' => $ansText];
                }
                $this->createAnswersForQuestion($question->id, [
                    'answer_type' => 'multiple_choice',
                    'answers' => $answersForService,
                    'correct_answer' => $correct,
                ]);
                $imported++;
                Log::info('Đã lưu câu hỏi thành công', ['question_id' => $question->id]);
            } catch (Exception $e) {
                Log::error('Lỗi khi lưu câu hỏi từ file import', [
                    'error' => $e->getMessage(),
                    'block' => $block
                ]);
                continue;
            }
        }

        Log::info("Import file txt hoàn tất. Tổng số câu hỏi import: {$imported}");

        return redirect()->route('admin.quizzes.edit', $quizId)
            ->with('success', "Đã import thành công {$imported} câu hỏi từ file.");
    }

    public function sort(array $questions): void
    {
        foreach ($questions as $item) {
            $this->questionRepo->update($item['id'], ['order' => $item['order']]);
        }
    }

    protected function updateQuestion(int $questionId, array $data): void
    {
        $question = $this->questionRepo->findById($questionId);
        if ($question && $question->order != ($data['order'] ?? 0)) {
            $this->ensureUniqueOrder($data['quiz_id'], $data['order'] ?? 0, $questionId);
        }

        $this->questionRepo->update($questionId, [
            'quiz_id'  => $data['quiz_id'],
            'question' => $data['question'],
            'order'    => $data['order'] ?? 0,
            'type'     => $data['answer_type'],
            'image'    => $data['image'] ?? $question->image,
        ]);
    }

    protected function createAnswersForQuestion(int $questionId, array $data): void
    {
        switch ($data['answer_type']) {
            case 'multiple_choice':
                foreach ($data['answers'] as $id => $answer) {
                    if (!empty(trim($answer['text'] ?? ''))) {
                        $this->answerRepo->create([
                            'question_id' => $questionId,
                            'answer' => $answer['text'],
                            'is_correct' => ((string)$data['correct_answer'] === (string)$id),
                        ]);
                    }
                }
                break;

            case 'text_input':
                $this->answerRepo->create([
                    'question_id' => $questionId,
                    'answer' => $data['text_answer'],
                    'is_correct' => true,
                ]);
                break;

            case 'true_false':
                $correct = (int)$data['correct_answer'] === 1;
                $this->answerRepo->createMany([
                    ['question_id' => $questionId, 'answer' => 'Đúng', 'is_correct' => $correct],
                    ['question_id' => $questionId, 'answer' => 'Sai', 'is_correct' => !$correct],
                ]);
                break;

            default:
                throw new Exception('Loại câu hỏi không hợp lệ.');
        }
    }

    protected function updateAnswersForQuestion(int $questionId, array $data): void
    {
        $this->answerRepo->deleteByQuestionId($questionId);
        $this->createAnswersForQuestion($questionId, $data);
    }

    protected function ensureUniqueOrder($quizId, $order, $ignoreQuestionId = null)
    {
        $query = $this->questionRepo->query()
            ->where('quiz_id', $quizId)
            ->where('order', '>=', $order);

        if ($ignoreQuestionId) {
            $query->where('id', '!=', $ignoreQuestionId);
        }

        $questions = $query->orderBy('order')->get();

        foreach ($questions as $q) {
            $q->order = $q->order + 1;
            $q->save();
        }
    }
}