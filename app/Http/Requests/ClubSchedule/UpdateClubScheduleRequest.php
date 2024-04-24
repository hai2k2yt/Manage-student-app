<?php

namespace App\Http\Requests\ClubSchedule;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClubScheduleRequest extends FormRequest
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
            'schedule_name' => 'nullable|string|max:255',
            'club_code' => 'nullable|exists:clubs,club_code',
            'teacher_code' => 'nullable|exists:teachers,teacher_code',
            'day_of_week' => 'nullable|in:1,2,3,4,5,6,7',
        ];
    }

    public function messages(): array
    {
        return [
            'club_code.exists' => __('validation.exists'),
            'teacher_code.exists' => __('validation.exists'),
            'day_of_week.in' => __('validation.in'),
        ];
    }
}
