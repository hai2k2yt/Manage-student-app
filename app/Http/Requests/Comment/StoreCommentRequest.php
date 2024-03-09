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
            'rating' => 'required|numeric'
        ];
    }

    public function messages(): array
    {
        return [
            'club_session_id.required' => __('club_session_id.required'),
            'club_session_id.exists' => __('club_session_id.not_existed'),
            'student_id.required' => __('student_id.required'),
            'student_id.exists' => __('student_id.not_existed'),
            'comment_text.required' => __('comment_text.required'),
            'comment_text.string' => __('comment_text.must_be_string'),
            'rating.required' => __('rating.required'),
            'rating.numeric' => __('rating.not_valid')
        ];
    }
}
