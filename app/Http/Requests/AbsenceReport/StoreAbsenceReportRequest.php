<?php

namespace App\Http\Requests\AbsenceReport;

use App\Enums\AbsenceReportEnum;
use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'session_code' => 'required|exists:club_sessions,session_code',
            'student_code' => 'required|exists:students,student_code',
            'reason' => 'required|string',
            'status' => [
                'required',
                Rule::in(AbsenceReportEnum::values())
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'session_code.required' => __('validation.required'),
            'session_code.exists' => __('validation.exists'),
            'student_code.required' => __('validation.required'),
            'student_code.exists' => __('validation.exists'),
            'reason.required' => __('validation.required'),
            'reason.string' => __('validation.string'),
            'status.required' => __('validation.required'),
            'status.in' => __('validation.in'),
        ];
    }
}
