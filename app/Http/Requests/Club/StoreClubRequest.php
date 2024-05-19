<?php

namespace App\Http\Requests\Club;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubRequest extends FormRequest
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
            'club_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'teacher_code' => 'required|exists:teachers,teacher_code'
        ];
    }

    public function messages(): array
    {
        return [
            'club_code.required' => __('validation.required', ['attribute' => __('club.field.club_code')]),
            'club_code.string' => __('validation.string', ['attribute' => __('club.field.club_code')]),
            'club_code.max' => __('validation.max', ['attribute' => __('club.field.club_code')]),
            'name.required' => __('validation.required', ['attribute' => __('club.field.name')]),
            'name.string' => __('validation.string', ['attribute' => __('club.field.name')]),
            'name.max' => __('validation.max', ['attribute' => __('club.field.name')]),
            'teacher_code.required' => __('validation.required', ['attribute' => __('club.field.teacher_code')]),
            'teacher_code.exists' => __('validation.exists', ['attribute' => __('club.field.teacher_code')]),
        ];
    }
}
