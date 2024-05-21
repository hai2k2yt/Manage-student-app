<?php

namespace App\Http\Requests\User;

use App\Enums\AbsenceReportEnum;
use App\Enums\RoleEnum;
use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'role' => [
                'nullable',
                Rule::in(RoleEnum::values())
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.string', ['attribute' => __('user.field.name')]),
            'name.max' => __('validation.max', ['attribute' => __('user.field.name')]),
            'role.in' => __('validation.in', ['attribute' => __('user.field.role')]),
        ];
    }
}
