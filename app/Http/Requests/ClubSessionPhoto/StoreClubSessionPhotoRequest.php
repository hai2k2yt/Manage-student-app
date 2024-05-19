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
            'session_code' => 'required|exists:club_sessions,session_code',
            'photo_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'session_code.required' => __('validation.required', ['attribute' => __('club_session_photo.field.session_code')]),
            'session_code.exists' => __('validation.exists', ['attribute' => __('club_session_photo.field.session_code')]),
            'photo_url.required' => __('validation.required', ['attribute' => __('club_session_photo.field.photo_url')]),
            'photo_url.image' => __('validation.image', ['attribute' => __('club_session_photo.field.photo_url')]),
            'photo_url.mimes' => __('validation.mimes', ['attribute' => __('club_session_photo.field.photo_url')]),
            'photo_url.max' => __('validation.max', ['attribute' => __('club_session_photo.field.photo_url')]),
        ];
    }
}
