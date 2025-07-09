<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function __construct(protected QuestionService $questionService)
    {
    }

    public function create($quizId)
    {

        $quiz = $this->questionService->getQuizById($quizId);

        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        $questions = $this->questionService->getByQuizId($quizId);

        return view('admin.quizzes.question.create', compact('quiz', 'questions'));
    }

    public function store(QuestionRequest $request, $quizId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;
        
        Log::info('Dữ liệu đã validate trước khi gửi tới QuestionService:', $validatedData);

        try {
         
            $this->questionService->createWithAnswers($validatedData);
            return redirect()->route('admin.quizzes.edit', $quizId)
                ->with('success', 'Câu hỏi đã được thêm thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Không thể tạo câu hỏi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($quizId, $questionId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        $question = $this->questionService->getByQuizIdAndQuestionId($quizId, $questionId);

        if (!$quiz || !$question) {
            abort(404, 'Quiz or question not found');
        }

        $answersRaw = $this->questionService->getAnswerByQuestionId($questionId) ?? collect();
        $answers = $answersRaw->map(function($item) {
            return [
                'id' => $item->id,
                'answer' => $item->answer,
                'text' => $item->answer,
                'is_correct' => (bool) $item->is_correct,
            ];
        });

        return view('admin.quizzes.question.edit', compact('quiz', 'question', 'answers'));
    }

    public function update(QuestionRequest $request, $quizId, $questionId)
    {
        $validatedData = $request->validated();
        $validatedData['quiz_id'] = $quizId;

        Log::info('Dữ liệu gửi từ form (update):', $validatedData);

        // Thêm dòng này để debug dữ liệu
        // Xóa sau khi kiểm tra xong
       

        try {
            $this->questionService->updateWithAnswers($questionId, $validatedData);
            return redirect()->route('admin.quizzes.edit', $quizId)
                ->with('success', 'Cập nhật câu hỏi thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Không thể cập nhật câu hỏi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($quizId, $questionId)
    {
        try {
            $this->questionService->delete($questionId);
            return redirect()->route('admin.quizzes.edit', $quizId)
                ->with('success', 'Xóa câu hỏi thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa câu hỏi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Không thể xóa câu hỏi: ' . $e->getMessage()]);
        }
    }

    // Trang chọn phương thức import (GET)
    public function import($quizId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }
        return view('admin.quizzes.import', compact('quiz'));
    }



    // Tải file mẫu (GET)
    public function downloadTemplate()
    {
        return response()->download(public_path('templates/question_import_template.xlsx'));
    }

    // Handle paste questions import (POST)
    public function importText($quizId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        $questionsText = request('questions_text');
        Log::info('Nội dung paste import:', ['content' => $questionsText]);

        // Tách từng block câu hỏi (2 dòng trống hoặc nhiều)
        $blocks = preg_split('/\R{2,}/', trim($questionsText));
        $order = 1;
        $imported = 0;

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $block))));
            Log::info('Block đang xử lý (paste):', ['block' => $block, 'lines' => $lines]);
            if (count($lines) < 6) continue;

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

            if (!$questionText || count($answers) < 2 || !$correct || !isset($answers[$correct])) continue;

            try {
                $question = $this->questionService->createQuestion([
                    'quiz_id' => $quizId,
                    'question' => $questionText,
                    'order' => $order++,
                    'answer_type' => 'multiple_choice',
                ]);
                $answersForService = [];
                foreach ($answers as $label => $ansText) {
                    $answersForService[$label] = ['text' => $ansText];
                }
                $this->questionService->createAnswersForQuestion($question->id, [
                    'answer_type' => 'multiple_choice',
                    'answers' => $answersForService,
                    'correct_answer' => $correct,
                ]);
                $imported++;
                Log::info('Đã lưu câu hỏi thành công (paste)', ['question_id' => $question->id]);
            } catch (\Exception $e) {
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

    // Handle file upload for import (POST)
    public function importFile(Request $request, $quizId)
    {
        $quiz = $this->questionService->getQuizById($quizId);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }

        $request->validate([
            'import_file' => 'required|file|max:25600', // 25MB
        ]);

        $file = $request->file('import_file');
        $ext = strtolower($file->getClientOriginalExtension());

        // Chỉ hỗ trợ txt hoặc csv
        if (!in_array($ext, ['txt', 'csv'])) {
            return redirect()->back()->withErrors(['import_file' => 'Chỉ hỗ trợ file .txt hoặc .csv']);
        }

        $content = file_get_contents($file->getRealPath());

        Log::info('Nội dung file import:', ['content' => $content]);

        // Tách từng block câu hỏi
        $blocks = preg_split('/\R{2,}/', trim($content));
        $order = 1;
        $imported = 0;

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $block))));
            Log::info('Block đang xử lý:', ['block' => $block, 'lines' => $lines]);
            if (count($lines) < 6) continue; // Ít nhất 6 dòng: câu hỏi, 4 đáp án, đáp án đúng

            $questionText = array_shift($lines);
            $answers = [];
            $correct = null;

            // Lấy 4 đáp án
            foreach (['A', 'B', 'C', 'D'] as $idx => $label) {
                if (!isset($lines[$idx])) continue;
                $ansText = preg_replace('/^[A-D]\.\s*/', '', $lines[$idx]);
                $answers[$label] = $ansText;
            }

            // Lấy đáp án đúng
            foreach ($lines as $line) {
                // Sửa lại regex để nhận cả "Answer: B" và "Đáp án: B"
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

            if (!$questionText || count($answers) < 2 || !$correct || !isset($answers[$correct])) continue;

            // Lưu vào DB
            try {
                $question = $this->questionService->createQuestion([
                    'quiz_id' => $quizId,
                    'question' => $questionText,
                    'order' => $order++,
                    'answer_type' => 'multiple_choice',
                ]);
                // Chỉ tạo 1 lần cho tất cả đáp án
                $answersForService = [];
                foreach ($answers as $label => $ansText) {
                    $answersForService[$label] = ['text' => $ansText];
                }
                $this->questionService->createAnswersForQuestion($question->id, [
                    'answer_type' => 'multiple_choice',
                    'answers' => $answersForService,
                    'correct_answer' => $correct,
                ]);
                $imported++;
                Log::info('Đã lưu câu hỏi thành công', ['question_id' => $question->id]);
            } catch (\Exception $e) {
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
}