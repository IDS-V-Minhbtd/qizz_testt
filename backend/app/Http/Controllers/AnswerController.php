<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of answers (API test).
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'AnswerController index',
            'data' => [] // bạn có thể trả dữ liệu thực tế ở đây
        ]);
    }

    /**
     * Store a new answer (example method).
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'required|string',
            'is_correct' => 'required|boolean'
        ]);

        // Ví dụ: lưu vào DB (giả sử bạn có model Answer)
        // $answer = Answer::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Answer created successfully',
            // 'data' => $answer
        ], 201);
    }
}
