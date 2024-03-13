<?php

namespace App\Http\Requests\Notification;

use App\Traits\ApiFailedValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
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
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required',
            'notification_type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'sender_id.required' => __('validation.required'),
            'sender_id.exists' => __('validation.exists'),
            'receiver_id.required' => __('validation.required'),
            'notification_type.required' => __('validation.required'),
            'comment_text.string' => __('validation.string'),
            'title.required' => __('validation.required'),
            'title.string' => __('validation.string'),
            'message.required' => __('validation.required'),
            'message.string' => __('validation.string')
        ];
    }
}
