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
            'session_name' => 'nullable|string|max:255',
            'schedule_code' => 'nullable|exists:club_schedules,schedule_code',
            'date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_code.exists' => __('validation.exists'),
            'date.date' => __('validation.date'),
        ];
    }
}
