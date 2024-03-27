<?php

namespace App\Http\Requests\Attendance;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateManyAttendanceRequest extends FormRequest
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
            'present' => 'nullable|array',
            'present.*' => 'exists:students,id',
            'permission_absence' => 'nullable|array',
            'permission_absence.*' => 'exists:students,id',
            'unexcused_absence' => 'nullable|array',
            'unexcused_absence.*' => 'exists:students,id'

        ];
    }

    public function messages(): array
    {
        return [
            'present.*.exists' => __('validation.exists'),
            'permission_absence.*.exists' => __('validation.exists'),
            'unexcused_absence.*.exists' => __('validation.exists'),
        ];
    }
}
