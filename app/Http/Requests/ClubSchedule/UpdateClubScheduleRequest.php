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
            'club_id' => 'sometimes|required|exists:clubs,id',
            'teacher_id' => 'sometimes|required|exists:users,id',
            'day_of_week' => 'sometimes|required|in:1,2,3,4,5,6,7',
            'start_time' => 'sometimes|required|date_format:H:i|before:end_time',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'club_id.required' => __('club_id.required'),
            'club_id.exists' => __('club_id.not_existed'),
            'teacher_id.required' => __('teacher_id.required'),
            'teacher_id.exists' => __('teacher_id.not_existed'),
            'day_of_week.required' => __('day_of_week.required'),
            'day_of_week.in' => __('day_of_week.not_valid'),
            'start_time.required' => __('start_time.required'),
            'start_time.date_format' => __('start_time.wrong_date_format'),
            'start_time.before' => __('start_time.before_end_time'),
            'end_time.required' => __('end_time.required'),
            'end_time.date_format' => __('end_time.wrong_date_format'),
            'end_time.after' => __('end_time.after_start_time'),
        ];
    }
}
