<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Allow all authorized users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'question' => 'required|string|max:500',
            'order' => 'nullable|integer|min:1',
            'correct_answer' => 'required|in:1,2,3,4',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'question.required' => 'The question text is required.',
            'question.max' => 'The question text must not exceed 500 characters.',
            'order.integer' => 'The order must be a valid number.',
            'order.min' => 'The order must be at least 1.',
            'answer_type.required' => 'The answer type is required.',
            'answer_type.in' => 'The answer type must be either multiple_choice or text_input.',
        ];
    }
}
