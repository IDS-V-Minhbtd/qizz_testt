<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Nếu có logic prepareForValidation khác không liên quan đến answer_type, giữ lại ở đây
    }

    public function rules()
    {
        // ✅ Normalize key của answers
        if ($this->has('answers')) {
            $normalized = collect($this->input('answers'))->values()->toArray();
            $this->merge(['answers' => $normalized]);
        }

        $rules = [
            'question' => ['required', 'string', 'max:255'],
            'order' => 'required|integer|min:1|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB
        ];

        if ($this->has('answers')) {
            $rules['answers'] = 'required|array|min:2';
            $rules['answers.*.text'] = 'required|string|max:255';
            $rules['correct_answer'] = [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $answers = $this->input('answers', []);
                    if (!array_key_exists($value, $answers)) {
                        $fail('Đáp án đúng không hợp lệ: Chỉ số ' . $value . ' không tồn tại trong danh sách đáp án.');
                    }
                },
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'question.required' => 'Câu hỏi không được để trống.',
            'question.string' => 'Câu hỏi phải là chuỗi ký tự.',
            'question.max' => 'Câu hỏi không được vượt quá 255 ký tự.',
            'question.unique' => 'Câu hỏi này đã tồn tại trong quiz này.',

            'order.required' => 'Thứ tự không được để trống.',
            'order.integer' => 'Thứ tự phải là số nguyên.',
            'order.min' => 'Thứ tự phải lớn hơn hoặc bằng 1.',

          
            'answers.required' => 'Cần ít nhất 2 đáp án cho câu hỏi dạng lựa chọn.',
            'answers.array' => 'Danh sách đáp án phải là một mảng.',
            'answers.min' => 'Cần ít nhất 2 đáp án cho câu hỏi dạng lựa chọn.',
            'answers.*.text.required' => 'Mỗi đáp án không được để trống.',
            'answers.*.text.string' => 'Mỗi đáp án phải là chuỗi ký tự.',
            'answers.*.text.max' => 'Mỗi đáp án không được vượt quá 255 ký tự.',

            'correct_answer.required' => 'Vui lòng chọn một đáp án đúng.',
            'correct_answer.integer' => 'Đáp án đúng phải là một chỉ số hợp lệ.',
            'correct_answer.in' => 'Đáp án đúng cho câu hỏi Đúng/Sai phải là "0" hoặc "1".',

            'text_answer.required' => 'Đáp án văn bản không được để trống.',
            'text_answer.string' => 'Đáp án văn bản phải là chuỗi ký tự.',
            'text_answer.max' => 'Đáp án văn bản không được vượt quá 255 ký tự.',

            'import_file.required' => 'Vui lòng chọn một tệp để nhập.',
            'import_file.file' => 'Tệp nhập vào không hợp lệ.',
            'import_file.mimes' => 'Tệp nhập vào phải có định dạng txt hoặc csv.',
            'import_file.max' => 'Kích thước tệp nhập vào không được vượt quá 25MB.',

            // ✅ Thêm lỗi cho ảnh
            'image.image' => 'Tệp tải lên phải là ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
    
