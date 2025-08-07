<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuestionService;
use App\Http\Requests\QuestionRequest;
use App\Models\Quiz;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller
{
    protected QuestionService $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    // Danh sách câu hỏi theo quiz
    public function index(Request $request, $quizId)
    {
        $questions = $this->questionService->paginateByQuizId($quizId, 10);
        return response()->json($questions);
    }

    // Tạo mới câu hỏi
    public function store(QuestionRequest $request)
    {
        $question = $this->questionService->create($request->validated());
        return response()->json($question, Response::HTTP_CREATED);
    }

    // Chi tiết một câu hỏi
    public function show($id)
    {
        $question = $this->questionService->getById($id);
        return response()->json($question);
    }

    // Cập nhật câu hỏi
    public function update(QuestionRequest $request, $id)
    {
        $updated = $this->questionService->update($id, $request->validated());
        return response()->json(['message' => 'Updated successfully', 'data' => $updated]);
    }

    // Xóa câu hỏi
    public function destroy($id)
    {
        $this->questionService->delete($id);
        return response()->json(['message' => 'Deleted successfully']);
    }

    // Import từ văn bản
    public function importText(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'content' => 'required|string'
        ]);

        $result = $this->questionService->importText($request->quiz_id, $request->content);
        return response()->json(['message' => 'Imported successfully', 'data' => $result]);
    }

    // Import từ file
    public function import(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'file' => 'required|file|mimes:txt,csv,xlsx'
        ]);

        $result = $this->questionService->importFile($request->quiz_id, $request->file);
        return response()->json(['message' => 'File imported successfully', 'data' => $result]);
    }

    // Preview câu hỏi
    public function preview(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $preview = $this->questionService->preview($request->content);
        return response()->json(['preview' => $preview]);
    }

    // Sắp xếp lại câu hỏi
    public function sort(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|integer|exists:questions,id',
            'questions.*.order' => 'required|integer'
        ]);

        $this->questionService->sort($request->questions);
        return response()->json(['message' => 'Sorted successfully']);
    }
}
