<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    /**
     * Xác định liệu người dùng có quyền thực hiện yêu cầu này hay không.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Thường bạn sẽ kiểm tra quyền người dùng ở đây, ví dụ: auth()->user()->isAdmin()
    }

    /**
     * Lấy các quy tắc xác thực cho yêu cầu.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question' => 'required|string|max:255', // Câu hỏi phải có và là chuỗi, tối đa 255 ký tự
            'order' => 'required|integer|min:1',     // Thứ tự phải là số nguyên và tối thiểu 1
            'answer_type' => 'required|string|in:multiple_choice,text_input,true_false', // Loại câu trả lời phải thuộc 3 loại đã cho
            'answers' => 'required_if:answer_type,multiple_choice|array', // Nếu là "multiple_choice", cần có mảng các đáp án
            'answers.*' => 'required_if:answer_type,multiple_choice|string|max:255', // Mỗi đáp án phải là chuỗi, tối đa 255 ký tự
            'text_answer' => 'required_if:answer_type,text_input|string|max:255', // Đáp án cho loại "text_input" phải là chuỗi và không quá 255 ký tự
            'correct_answer' => 'required_if:answer_type,true_false|in:0,1', // Đáp án cho loại "true_false" phải là 0 hoặc 1
        ];
    }

    /**
     * Xử lý thông báo lỗi sau khi xác thực.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'question.required' => 'Câu hỏi không được để trống.',
            'order.required' => 'Thứ tự không được để trống.',
            'answer_type.required' => 'Loại câu trả lời không được để trống.',
            'answers.required_if' => 'Vui lòng cung cấp các đáp án nếu loại câu trả lời là "Lựa chọn nhiều".',
            'text_answer.required_if' => 'Vui lòng cung cấp đáp án văn bản nếu loại câu trả lời là "Nhập văn bản".',
            'correct_answer.required_if' => 'Vui lòng chọn đáp án đúng hay sai nếu loại câu trả lời là "Đúng/Sai".',
        ];
    }
}
