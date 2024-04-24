<?php

namespace App\Http\Requests\StudentClass;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentClassRequest extends FormRequest
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
            'class_name' => 'nullable|string|max:255',
            'teacher_code' => 'nullable|exists:teachers,teacher_code'
        ];
    }

    public function messages(): array
    {
        return [
            'class_name.string' => __('validation.string'),
            'class_name.max' => __('validation.max'),
            'teacher_code.exists' => __('validation.exists'),
        ];
    }
}
