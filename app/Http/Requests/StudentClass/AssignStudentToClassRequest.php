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
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id'
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => __('validation.required'),
            'student_ids.array' => __('validation.array'),
            'student_ids.*.required' => __('validation.required'),
            'student_ids.*.exists' => __('validation.exists'),
            'class_id.required' => __('validation.required'),
            'class_id.exists' => __('validation.exists')
        ];
    }
}
