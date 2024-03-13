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
            'schedule_id' => 'required|exists:club_schedules,id',
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => __('validation.required'),
            'schedule_id.exists' => __('validation.exists'),
            'date.required' => __('validation.required'),
            'date.date' => __('validation.date'),
        ];
    }
}
