<?php

namespace App\Http\Requests\ClubSession;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClubSessionRequest extends FormRequest
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
            'schedule_id' => 'sometimes|required|exists:club_schedules,id',
            'date' => 'sometimes|required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => __('schedule_id.required'),
            'schedule_id.exists' => __('schedule_id.not_existed'),
            'date.required' => __('date.required'),
            'date.date' => __('date.date'),
        ];
    }
}
