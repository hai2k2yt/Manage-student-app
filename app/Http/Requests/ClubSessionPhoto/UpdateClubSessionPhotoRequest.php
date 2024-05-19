<?php

namespace App\Http\Requests\ClubSessionPhoto;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClubSessionPhotoRequest extends FormRequest
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
            'photo_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'photo_url.required' => __('validation.required', ['attribute' => __('club_session_photo.field.photo_url')]),
            'photo_url.image' => __('validation.image', ['attribute' => __('club_session_photo.field.photo_url')]),
            'photo_url.mimes' => __('validation.mimes', ['attribute' => __('club_session_photo.field.photo_url')]),
            'photo_url.max' => __('validation.max', ['attribute' => __('club_session_photo.field.photo_url')]),
        ];
    }
}
