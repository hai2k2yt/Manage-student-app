<?php

namespace App\Http\Requests\Comment;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    use ApiFailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'session_code' => 'required|exists:club_sessions,session_code',
            'student_code' => 'required|exists:students,student_code',
            'content' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'session_code.required' => __('validation.required', ['attribute' => __('comment.field.session_code')]),
            'session_code.exists' => __('validation.exists', ['attribute' => __('comment.field.session_code')]),
            'student_code.required' => __('validation.required', ['attribute' => __('comment.field.student_code')]),
            'student_code.exists' => __('validation.exists', ['attribute' => __('comment.field.student_code')]),
            'content.required' => __('validation.required', ['attribute' => __('comment.field.content')]),
            'content.string' => __('validation.string', ['attribute' => __('comment.field.content')]),
        ];
    }
}
