<?php

namespace App\Http\Requests\Student;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'student_code' => 'required|string|max:255|unique:students,student_code',
            'name' => 'required|string|max:255',
            'user_id' => 'sometimes|nullable|exists:users,id',
            'class_code' => 'sometimes|nullable|exists:classes,class_code'
        ];
    }

    public function messages(): array
    {
        return [
            'student_code.required' => __('validation.required'),
            'student_code.string' => __('validation.string'),
            'name.required' => __('validation.required'),
            'name.string' => __('validation.string'),
            'name.max' => __('validation.max'),
            'user_id.exists' => __('validation.exists'),
            'class_code.exists' => __('validation.exists'),
        ];
    }
}
