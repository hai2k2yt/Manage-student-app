<?php

namespace App\Http\Requests\StudentClass;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignStudentToClassRequest extends FormRequest
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
            'student_codes' => 'required|array',
            'student_codes.*' => 'required|exists:students,student_code',
            'class_code' => 'required|exists:classes,class_code'
        ];
    }

    public function messages(): array
    {
        return [
            'student_codes.required' => __('validation.required', ['attribute' => __('student_class.field.student_code')]),
            'student_codes.array' => __('validation.array', ['attribute' => __('student_class.field.student_code')]),
            'student_codes.*.required' => __('validation.required', ['attribute' => __('student_class.field.student_code')]),
            'student_codes.*.exists' => __('validation.exists', ['attribute' => __('student_class.field.student_code')]),
            'class_code.required' => __('validation.required', ['attribute' => __('student_class.field.class_code')]),
            'class_code.exists' => __('validation.exists', ['attribute' => __('student_class.field.class_code')])
        ];
    }
}
