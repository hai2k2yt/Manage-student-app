<?php

namespace App\Http\Requests\StudentClass;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentClassRequest extends FormRequest
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
            'class_name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id'
        ];
    }

    public function messages(): array
    {
        return [
            'class_name.required' => __('class_name.required'),
            'class_name.string' => __('class_name.string'),
            'class_name.max' => __('class_name.max'),
            'teacher_id.required' => __('teacher_id.required'),
            'teacher_id.exists' => __('teacher_id.exists'),
        ];
    }
}
