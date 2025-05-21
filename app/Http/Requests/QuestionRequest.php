<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Hoặc kiểm tra quyền truy cập
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
            $rules['answers.*.text'] = 'required|string|max:255';
            $rules['correct_answer'] = 'required|integer|min:0';
        }

        if ($this->input('answer_type') === 'text_input') {
            $rules['text_answer'] = 'required|string|max:255';
        }

        if ($this->input('answer_type') === 'true_false') {
            $rules['correct_answer'] = 'required|in:0,1';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('answer_type') === 'multiple_choice' && $this->filled('correct_answer')) {
                $answers = $this->input('answers', []);
                $correctAnswer = (int) $this->input('correct_answer');
                if (!array_key_exists($correctAnswer, $answers)) {
                    $validator->errors()->add(
                        'correct_answer',
                        'Đáp án đúng không hợp lệ: Chỉ số ' . $correctAnswer . ' không tồn tại trong danh sách đáp án.'
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'question.required' => 'Câu hỏi không được để trống.',
            'question.string' => 'Câu hỏi phải là chuỗi ký tự.',
            'question.max' => 'Câu hỏi không được vượt quá 255 ký tự.',
            'order.required' => 'Thứ tự không được để trống.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 1.',
            'answer_type.required' => 'Loại câu trả lời không được để trống.',
            'answer_type.in' => 'Loại câu trả lời không hợp lệ.',
            'answers.required' => 'Cần ít nhất 2 đáp án cho câu hỏi dạng lựa chọn.',
            'answers.array' => 'Danh sách đáp án phải là một mảng.',
            'answers.min' => 'Cần ít nhất 2 đáp án cho câu hỏi dạng lựa chọn.',
            'answers.*.text.required' => 'Mỗi đáp án không được để trống.',
            'answers.*.text.string' => 'Mỗi đáp án phải là chuỗi ký tự.',
            'answers.*.text.max' => 'Mỗi đáp án không được vượt quá 255 ký tự.',
            'correct_answer.required' => 'Vui lòng chọn một đáp án đúng.',
            'correct_answer.integer' => 'Đáp án đúng phải là số nguyên.',
            'correct_answer.min' => 'Đáp án đúng phải là số không âm.',
            'correct_answer.in' => 'Đáp án đúng cho câu hỏi Đúng/Sai phải là 0 hoặc 1.',
            'text_answer.required' => 'Đáp án văn bản không được để trống.',
            'text_answer.string' => 'Đáp án văn bản phải là chuỗi ký tự.',
            'text_answer.max' => 'Đáp án văn bản không được vượt quá 255 ký tự.',
        ];
    }
}