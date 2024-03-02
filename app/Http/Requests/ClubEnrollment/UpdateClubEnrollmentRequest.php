<?php

namespace App\Http\Requests\ClubEnrollment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClubEnrollmentRequest extends FormRequest
{
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
            'student_id' => 'sometimes|required|exists:students,id',
            'club_id' => 'sometimes|required|exists:clubs,id'
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => __('student_id.required'),
            'student_id.exists' => __('student_id.not_existed'),
            'club_id.required' => __('club_id.required'),
            'club_id.exists' => __('club_id.not_existed'),
        ];
    }
}
