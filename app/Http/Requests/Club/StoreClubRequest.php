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
            'name.required' => __('validation.required'),
            'name.string' => __('validation.string'),
            'name.max' => __('validation.max'),
            'teacher_code.required' => __('validation.required'),
            'teacher_code.exists' => __('validation.exists'),
        ];
    }
}
