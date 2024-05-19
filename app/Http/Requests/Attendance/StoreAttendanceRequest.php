<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceEnum;
use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'session_code' => 'required|exists:club_sessions,session_code',
            'student_code' => 'required|exists:students,student_code',
            'present' => [
                'required',
                Rule::in(AttendanceEnum::values())
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'session_code.required' => __('validation.required', ['attribute' => __('attendance.field.session_code')]),
            'session_code.exists' => __('validation.exists', ['attribute' => __('attendance.field.session_code')]),
            'student_code.required' => __('validation.required', ['attribute' => __('attendance.field.student_code')]),
            'student_code.exists' => __('validation.exists', ['attribute' => __('attendance.field.student_code')]),
            'present.required' => __('validation.required', ['attribute' => __('attendance.field.present')]),
            'present.in' => __('validation.in', ['attribute' => __('attendance.field.present')]),
        ];
    }
}
