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
            'club_id' => 'required|exists:clubs,id',
            'teacher_id' => 'nullable|exists:users,id',
            'day_of_week' => 'nullable|in:1,2,3,4,5,6,7',
            'start_time' => 'nullable|date_format:H:i|before:end_time',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'club_id.required' => __('validation.required'),
            'club_id.exists' => __('validation.exists'),
            'teacher_id.exists' => __('validation.exists'),
            'day_of_week.in' => __('validation.in'),
            'start_time.date_format' => __('validation.date_format'),
            'start_time.before' => __('validation.before'),
            'end_time.date_format' => __('validation.date_format'),
            'end_time.after' => __('validation.after'),
        ];
    }
}
