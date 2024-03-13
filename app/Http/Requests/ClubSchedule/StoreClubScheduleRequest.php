<?php

namespace App\Http\Requests\ClubSchedule;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubScheduleRequest extends FormRequest
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
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:1,2,3,4,5,6,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'club_id.required' => __('validation.required'),
            'club_id.exists' => __('validation.exists'),
            'teacher_id.required' => __('validation.required'),
            'teacher_id.exists' => __('validation.exists'),
            'day_of_week.required' => __('validation.required'),
            'day_of_week.in' => __('validation.in'),
            'start_time.required' => __('validation.required'),
            'start_time.date_format' => __('validation.date_format'),
            'end_time.required' => __('validation.required'),
            'end_time.date_format' => __('validation.date_format'),
            'end_time.after' => __('validation.after'),
        ];
    }
}
