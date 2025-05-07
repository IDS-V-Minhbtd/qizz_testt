<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Answer;

class QuizController extends Controller
{
    public function checkAnswer(Request $request)
    {
        $answerId = $request->input('answer_id');
        $questionId = $request->input('question_id');

        $answer = Answer::where('id', $answerId)->where('question_id', $questionId)->first();

        if (!$answer) {
            return response()->json([
                'correct' => false,
                'message' => 'Đáp án không hợp lệ.'
            ], 400);
        }

        return response()->json([
            'correct' => $answer->is_correct,
            'message' => $answer->is_correct ? ' Chính xác!' : ' Sai rồi!'
        ]);
    }
}
