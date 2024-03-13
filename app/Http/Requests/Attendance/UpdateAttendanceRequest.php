<?php

namespace App\Http\Requests\Attendance;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
            'present' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'present.required' => __('validation.required'),
            'present.boolean' => __('validation.boolean'),
        ];
    }
}
