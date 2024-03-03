<?php

namespace App\Http\Requests\AbsenceReport;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceReportRequest extends FormRequest
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
            'reason' => 'required|string',
            'status' => 'required|in:1,2,3',
        ];
    }

    public function messages(): array
    {
        return [
            'club_session_id.required' => __('club_session_id.required'),
            'club_session_id.exists' => __('club_session_id.not_existed'),
            'student_id.required' => __('student_id.required'),
            'student_id.exists' => __('student_id.not_existed'),
            'reason.required' => __('reason.required'),
            'reason.string' => __('reason.string_format'),
            'status.required' => __('status.required'),
            'status.in' => __('status.not_valid'),
        ];
    }
}
