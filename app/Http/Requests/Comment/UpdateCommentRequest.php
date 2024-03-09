<?php

namespace App\Http\Requests\Comment;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
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
            'comment_text' => 'nullable|string',
            'rating' => 'nullable|numeric'
        ];
    }

    public function messages(): array
    {
        return [
            'comment_text.string' => __('comment_text.must_be_string'),
            'rating.numeric' => __('rating.not_valid')
        ];
    }
}
