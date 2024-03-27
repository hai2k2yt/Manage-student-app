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
            'club_session_id' => 'required|exists:club_sessions,id',
            'student_id' => 'required|exists:students,id',
            'present' => [
                'required',
                Rule::in(AttendanceEnum::values())
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'club_session_id.required' => __('validation.required'),
            'club_session_id.exists' => __('validation.exists'),
            'student_id.required' => __('validation.required'),
            'student_id.exists' => __('validation.exists'),
            'present.required' => __('validation.required'),
            'present.in' => __('validation.in'),
        ];
    }
}
