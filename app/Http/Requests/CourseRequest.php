<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed (e.g., check if user is admin or quizz_master)
        return auth()->user()->role === 'admin' || auth()->user()->role === 'quizz_master';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'created_by' => 'nullable|exists:users,id',
            'tag_id' => 'nullable|exists:tags,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slug' => 'required|string',
        ];

        // Adjust slug uniqueness rule for update vs. create
        if ($this->route('id')) {
            $rules['slug'] .= '|unique:courses,slug,' . $this->route('id');
        } else {
            $rules['slug'] .= '|unique:courses,slug';
        }

        return $rules;
    }
}