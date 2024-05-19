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
            'session_code' => 'required|string|max:255|unique:club_sessions,session_code',
            'session_name' => 'required|string|max:255',
            'schedule_code' => 'required|exists:club_schedules,schedule_code',
            'date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'session_code.required' => __('validation.required', ['attribute' => __('club_session.field.session_code')]),
            'session_code.string' => __('validation.string', ['attribute' => __('club_session.field.session_code')]),
            'session_code.max' => __('validation.max', ['attribute' => __('club_session.field.session_code')]),
            'session_code.unique' => __('validation.unique', ['attribute' => __('club_session.field.session_code')]),

            'session_name.required' => __('validation.required', ['attribute' => __('club_session.field.session_name')]),
            'session_name.string' => __('validation.string', ['attribute' => __('club_session.field.session_name')]),
            'session_name.max' => __('validation.max', ['attribute' => __('club_session.field.session_name')]),

            'schedule_code.required' => __('validation.required', ['attribute' => __('club_session.field.schedule_code')]),
            'schedule_code.exists' => __('validation.exists', ['attribute' => __('club_session.field.schedule_code')]),

            'date.required' => __('validation.required', ['attribute' => __('club_session.field.date')]),
            'date.date' => __('validation.date', ['attribute' => __('club_session.field.date')]),
        ];
    }
}
