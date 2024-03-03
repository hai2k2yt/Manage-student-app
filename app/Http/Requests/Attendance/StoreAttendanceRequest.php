<?php

namespace App\Http\Requests\Attendance;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'present' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'club_session_id.required' => __('club_session_id.required'),
            'club_session_id.exists' => __('club_session_id.not_existed'),
            'student_id.required' => __('student_id.required'),
            'student_id.exists' => __('student_id.not_existed'),
            'present.required' => __('present.required'),
            'present.boolean' => __('present.wrong_format'),
        ];
    }
}
