<?php

namespace App\Http\Requests\Club;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClubRequest extends FormRequest
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
            'name' => 'sometimes|nullable|string|max:255',
            'teacher_code' => 'sometimes|nullable|exists:teachers,teacher_code'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.string', ['attribute' => __('club.field.name')]),
            'name.max' => __('validation.max', ['attribute' => __('club.field.name')]),
            'teacher_code.exists' => __('validation.exists', ['attribute' => __('club.field.teacher_code')]),
        ];
    }
}
