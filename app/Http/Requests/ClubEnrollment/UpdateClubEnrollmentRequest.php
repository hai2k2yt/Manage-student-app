<?php

namespace App\Http\Requests\ClubEnrollment;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClubEnrollmentRequest extends FormRequest
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
            'student_code' => 'sometimes|required|exists:students,student_code',
            'club_code' => 'sometimes|required|exists:clubs,club_code'
        ];
    }

    public function messages(): array
    {
        return [
            'student_code.required' => __('validation.required'),
            'student_code.exists' => __('validation.exists'),
            'club_code.required' => __('validation.required'),
            'club_code.exists' => __('validation.exists'),
        ];
    }
}
