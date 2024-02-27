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
            'name' => 'required|string|max:255',
            'user_id' => 'sometimes|nullable|exists:users,id',
            'class_id' => 'sometimes|nullable|exists:classes,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('name.required'),
            'name.string' => __('name.must_be_string'),
            'name.max' => __('name.max'),
            'user_id.exists' => __('user_id.not_existed'),
            'class_id.exists' => __('class_id.not_existed'),
        ];
    }
}
