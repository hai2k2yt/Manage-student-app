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
            'teacher_code' => 'nullable|exists:teachers,teacher_code',
            'day_of_week' => 'nullable|in:1,2,3,4,5,6,7',
            'student_fee' => 'nullable|numeric|gt:0',
            'teacher_fee' => 'nullable|numeric|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_name.string' => __('validation.exists', ['attribute' => __('club_schedule.field.schedule_name')]),
            'schedule_name.max' => __('validation.exists', ['attribute' => __('club_schedule.field.schedule_name')]),

            'teacher_code.exists' => __('validation.exists', ['attribute' => __('club_schedule.field.teacher_code')]),

            'day_of_week.in' => __('validation.in', ['attribute' => __('club_schedule.field.day_of_week')]),

            'student_fee.numeric' => __('validation.numeric', ['attribute' => __('club_schedule_fee.field.student_fee')]),
            'student_fee.gt' => __('validation.gt', ['attribute' => __('club_schedule_fee.field.student_fee')]),

            'teacher_fee.numeric' => __('validation.numeric', ['attribute' => __('club_schedule_fee.field.teacher_fee')]),
            'teacher_fee.gt' => __('validation.gt', ['attribute' => __('club_schedule_fee.field.teacher_fee')]),
        ];
    }
}
