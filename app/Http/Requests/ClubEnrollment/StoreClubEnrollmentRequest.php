<?php

namespace App\Http\Requests\ClubEnrollment;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubEnrollmentRequest extends FormRequest
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
            'student_code' => 'required|exists:students,student_code',
            'club_code' => 'required|exists:clubs,club_code',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
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
