<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAnswerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'question_id' => 'required|integer|exists:questions,id',
            'answer_id' => 'required|integer|exists:answers,id',
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'question_id.required' => 'Câu hỏi không được để trống.',
            'question_id.integer' => 'Câu hỏi phải là số.',
            'question_id.exists' => 'Câu hỏi không tồn tại.',
            'answer_id.required' => 'Đáp án không được để trống.',
            'answer_id.integer' => 'Đáp án phải là số.',
            'answer_id.exists' => 'Đáp án không tồn tại.',
            'user_id.required' => 'Người dùng không được để trống.',
            'user_id.integer' => 'Người dùng phải là số.',
            'user_id.exists' => 'Người dùng không tồn tại.',
        ];
    }
}