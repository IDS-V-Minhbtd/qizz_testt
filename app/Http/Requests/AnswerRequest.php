<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all authorized users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'question_id' => 'required|exists:questions,id',
            'answer'=> 'required|string|max:500',
            'answer_type' => 'required|in:multiple_choice,text_input,true_false',
        ];

        if ($this->input('answer_type') === 'multiple_choice') {
            $rules['answers'] = 'required|array|min:2';
            $rules['correct_answer'] = 'required|integer|between:1,4';
            foreach ($this->input('answers', []) as $key => $value) {
                $rules["answers.$key.text"] = 'required|string|max:500';
            }
        }

        if ($this->input('answer_type') === 'text_input') {
            $rules['text_answer'] = 'required|string|max:255';
        }

        if ($this->input('answer_type') === 'true_false') {
            $rules['correct_answer'] = 'required|in:1,2';
        }

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_id.required' => 'The question ID is required.',
            'question_id.exists' => 'The selected question ID does not exist.',
            'answer_type.required' => 'The answer type is required.',
            'answer_type.in' => 'The answer type must be either multiple_choice, text_input, or true_false.',
            'answers.required' => 'You must provide at least two answers.',
            'answers.*.text.required' => 'Each answer text is required.',
            'correct_answer.required' => 'You must specify the correct answer.',
            'correct_answer.between' => 'The correct answer must be between 1 and 4.',
            'text_answer.required' => 'You must provide a correct text answer.',
        ];
    }
}
