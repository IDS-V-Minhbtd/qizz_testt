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
        
        ];

 

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
         
           
            'answers.required' => 'You must provide at least two answers.',
            'answers.*.text.required' => 'Each answer text is required.',
            'correct_answer.required' => 'You must specify the correct answer.',
            'correct_answer.between' => 'The correct answer must be between 1 and 4.',
            'text_answer.required' => 'You must provide a correct text answer.',
        ];
    }
}
