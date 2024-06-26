<?php

namespace App\Http\Requests\ClubEnrollment;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelClubEnrollmentRequest extends FormRequest
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
            'to' => 'nullable|date_format:Y-m-d'
        ];
    }

    public function messages(): array
    {
        return [
            'to.date_format' => __('validation.date_format', ['attribute' => __('club_enrollment_history.field.to')]),
        ];
    }
}
