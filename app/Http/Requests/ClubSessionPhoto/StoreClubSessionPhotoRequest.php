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
            'session_id.required' => __('session_id.required'),
            'session_id.exists' => __('session_id.not_existed'),
            'photo_url.required' => __('photo_url.required'),
            'photo_url.image' => __('photo_url.not_image_format'),
            'photo_url.mimes' => __('photo_url.mimes_not_support'),
            'photo_url.max' => __('photo_url.size_too_large'),
        ];
    }
}
