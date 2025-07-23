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
            'is_public' => 'nullable|boolean',
            // Chỉ validate image nếu là file upload
            'image' => 'nullable',
            'slug' => 'required|string',
        ];

        // Nếu có file upload thì mới validate là ảnh
        if ($this->hasFile('image')) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        // Adjust slug uniqueness rule for update vs. create
        $courseId = $this->route('course') ?? $this->route('id');
        if ($courseId) {
            $rules['slug'] .= '|unique:courses,slug,' . $courseId;
        } else {
            $rules['slug'] .= '|unique:courses,slug';
        }

        return $rules;
    }
}