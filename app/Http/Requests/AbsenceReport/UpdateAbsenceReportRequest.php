<?php

namespace App\Http\Requests\AbsenceReport;

use App\Enums\AbsenceReportEnum;
use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAbsenceReportRequest extends FormRequest
{
    use ApiFailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'reason' => 'nullable|string',
            'status' => [
                'nullable',
                Rule::in(AbsenceReportEnum::values())
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.string' => __('validation.string', ['attribute' => __('absence_report.field.reason')]),
            'status.in' => __('validation.in', ['attribute' => __('absence_report.field.status')]),
        ];
    }
}
