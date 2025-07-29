<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'user_id'     => ['required', 'integer', 'exists:users,id'],

            // Chấp nhận 1 trong 2: answer_id hoặc answers[]
            'answer_id'   => ['nullable', 'integer', 'exists:answers,id'],
            'answers'     => ['nullable', 'array'],
            'answers.*'   => ['integer', 'exists:answers,id'],

            // Thêm điều kiện: ít nhất 1 trong 2 field phải có
            // sẽ kiểm tra thủ công trong withValidator()
        ];
    }

    public function messages(): array
    {
        return [
            'question_id.required' => 'Câu hỏi không được để trống.',
            'question_id.integer'  => 'Câu hỏi phải là số.',
            'question_id.exists'   => 'Câu hỏi không tồn tại.',

            'answer_id.integer'    => 'Đáp án phải là số.',
            'answer_id.exists'     => 'Đáp án không tồn tại.',

            'answers.array'        => 'Danh sách đáp án phải là mảng.',
            'answers.*.integer'    => 'Từng đáp án phải là số.',
            'answers.*.exists'     => 'Một hoặc nhiều đáp án không tồn tại.',

            'user_id.required'     => 'Người dùng không được để trống.',
            'user_id.integer'      => 'Người dùng phải là số.',
            'user_id.exists'       => 'Người dùng không tồn tại.',
        ];
    }

    /**
     * Custom logic: bắt buộc phải có answer_id hoặc answers[]
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->filled('answer_id') && !$this->filled('answers')) {
                $validator->errors()->add('answer', 'Phải chọn ít nhất một đáp án.');
            }
        });
    }
}
