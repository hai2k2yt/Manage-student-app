<?php

namespace App\Http\Requests\ClubEnrollment;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignStudentToClubRequest extends FormRequest
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
            'student_codes' => 'required|array',
            'student_codes.*' => 'required|exists:students,student_code',
            'club_code' => 'required|exists:clubs,club_code'
        ];
    }

    public function messages(): array
    {
        return [
            'student_codes.required' => __('validation.required'),
            'student_codes.array' => __('validation.array'),
            'student_codes.*.required' => __('validation.required'),
            'student_codes.*.exists' => __('validation.exists'),
            'club_code.required' => __('validation.required'),
            'club_code.exists' => __('validation.exists')
        ];
    }
}
