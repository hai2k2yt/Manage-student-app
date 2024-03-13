<?php

namespace App\Http\Requests\ClubSessionPhoto;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubSessionPhotoRequest extends FormRequest
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
            'session_id' => 'required|exists:club_sessions,id',
            'photo_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => __('validation.required'),
            'session_id.exists' => __('validation.exists'),
            'photo_url.required' => __('validation.required'),
            'photo_url.image' => __('validation.image'),
            'photo_url.mimes' => __('validation.mimes'),
            'photo_url.max' => __('validation.max'),
        ];
    }
}
