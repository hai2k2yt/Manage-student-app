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
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('name.required'),
            'name.string' => __('name.must_be_string'),
            'name.max' => __('name.max'),
            'teacher_id.required' => __('user_id.required'),
            'teacher_id.exists' => __('class_id.not_existed'),
        ];
    }
}
