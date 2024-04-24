<?php

namespace App\Http\Requests\ClubSession;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubSessionRequest extends FormRequest
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
            'session_code' => 'required|string|unique:club_sessions,session_code|max:255',
            'session_name' => 'required|string|max:255',
            'schedule_code' => 'required|exists:club_schedules,schedule_code',
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_code.required' => __('validation.required'),
            'schedule_code.exists' => __('validation.exists'),
            'date.required' => __('validation.required'),
            'date.date' => __('validation.date'),
        ];
    }
}
