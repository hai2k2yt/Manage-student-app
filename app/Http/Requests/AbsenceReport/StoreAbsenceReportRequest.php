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
            'club_session_id.required' => __('validation.required'),
            'club_session_id.exists' => __('validation.exists'),
            'student_id.required' => __('validation.required'),
            'student_id.exists' => __('validation.exists'),
            'reason.required' => __('validation.required'),
            'reason.string' => __('validation.string'),
            'status.required' => __('validation.required'),
            'status.in' => __('validation.in'),
        ];
    }
}
