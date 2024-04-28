<?php

namespace App\Http\Requests\Statistic;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StatisticStudentFeeRequest extends FormRequest
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
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ];
    }
}
