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
            'club_session_id' => 'required|exists:club_sessions,id',
            'student_id' => 'required|exists:students,id',
            'comment_text' => 'required|string',
            'rating' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'club_session_id.required' => __('validation.required'),
            'club_session_id.exists' => __('validation.exists'),
            'student_id.required' => __('validation.required'),
            'student_id.exists' => __('validation.exists'),
            'comment_text.required' => __('validation.required'),
            'comment_text.string' => __('validation.string'),
            'rating.required' => __('validation.required'),
            'rating.integer' => __('validation.integer')
        ];
    }
}
