<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
{
    $rules = [
        'question' => 'required|string|max:255',
        'order' => 'required|integer|min:1',
        'answer_type' => 'required|string|in:multiple_choice,text_input,true_false',
    ];

    if ($this->input('answer_type') === 'multiple_choice') {
        $rules['answers'] = 'required|array|min:2';
        $rules['answers.*'] = 'required|string|max:255';
        $rules['correct_answer'] = 'nullable|integer';
    }

    if ($this->input('answer_type') === 'text_input') {
        $rules['text_answer'] = 'required|string|max:255';
    }

    if ($this->input('answer_type') === 'true_false') {
        $rules['correct_answer'] = 'nullable|integer';
    }

    return $rules;
}


    public function messages()
    {
        return [
            'question.required' => 'Câu hỏi không được để trống.',
            'order.required' => 'Thứ tự không được để trống.',
            'answer_type.required' => 'Loại câu trả lời không được để trống.',
            'answers.required_if' => 'Cần ít nhất 2 đáp án cho câu hỏi dạng lựa chọn.',
            'answers.*.required' => 'Mỗi đáp án không được để trống.',
            'correct_answer.integer' => 'Đáp án đúng phải là số.',

          
        ];
    }
}
