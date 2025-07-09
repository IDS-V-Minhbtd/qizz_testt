<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:quizzes,code',
            'description' => 'nullable|string|max:1000',
            'time_limit' => 'nullable|integer|min:1|max:1440', // Time limit in minutes
            'is_public' => 'boolean',
            'catalog' => 'nullable|string|max:255', // New field for catalog
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
            'name.required' => 'The quiz name is required.',
            'name.max' => 'The quiz name must not exceed 255 characters.',
            'code.max' => 'The quiz code must not exceed 50 characters.',
            'code.unique' => 'The quiz code must be unique.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'time_limit.integer' => 'The time limit must be a valid number.',
            'time_limit.min' => 'The time limit must be at least 1 minute.',
            'time_limit.max' => 'The time limit must not exceed 1440 minutes.',
            'is_public.boolean' => 'The public status must be true or false.',
        ];
    }
}
