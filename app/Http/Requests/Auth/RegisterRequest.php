<?php

namespace App\Http\Requests\Auth;

use App\Enums\RoleEnum;
use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'username' => 'required|string|email|between:2,100|unique:users',
            'name' => 'required|string|between:2,100',
            'password' => 'required|string|confirmed|min:6',
            'role' => [
                'required',
                Rule::in(RoleEnum::values())
            ]
        ];
    }
}
