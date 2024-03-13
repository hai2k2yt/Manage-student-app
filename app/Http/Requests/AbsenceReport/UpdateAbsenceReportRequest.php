<?php

namespace App\Http\Requests\AbsenceReport;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsenceReportRequest extends FormRequest
{
    use ApiFailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'reason' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:1,2,3',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => __('validation.required'),
            'reason.string' => __('validation.string'),
            'status.required' => __('validation.required'),
            'status.in' => __('validation.in'),
        ];
    }
}
