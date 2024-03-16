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
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'club_id' => 'required|exists:clubs,id'
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => __('validation.required'),
            'student_ids.array' => __('validation.array'),
            'student_ids.*.required' => __('validation.required'),
            'student_ids.*.exists' => __('validation.exists'),
            'club_id.required' => __('validation.required'),
            'club_id.exists' => __('validation.exists')
        ];
    }
}
