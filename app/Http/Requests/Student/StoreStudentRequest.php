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
            'student_code.required' => __('validation.required', ['attribute' => __('student.field.student_code')]),
            'student_code.string' => __('validation.string', ['attribute' => __('student.field.student_code')]),
            'student_code.max' => __('validation.max', ['attribute' => __('student.field.student_code')]),
            'student_code.unique' => __('validation.unique', ['attribute' => __('student.field.student_code')]),
            'name.required' => __('validation.required', ['attribute' => __('student.field.name')]),
            'name.string' => __('validation.string', ['attribute' => __('student.field.name')]),
            'name.max' => __('validation.max', ['attribute' => __('student.field.name')]),
            'user_id.exists' => __('validation.exists', ['attribute' => __('student.field.user_id')]),
            'class_code.exists' => __('validation.exists', ['attribute' => __('student.field.class_code')]),
        ];
    }
}
