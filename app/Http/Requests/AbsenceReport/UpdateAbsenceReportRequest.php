<?php

namespace App\Http\Requests\AbsenceReport;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsenceReportRequest extends FormRequest
{
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
            'reason.required' => __('reason.required'),
            'reason.string' => __('reason.string_format'),
            'status.required' => __('status.required'),
            'status.in' => __('status.not_valid'),
        ];
    }
}
