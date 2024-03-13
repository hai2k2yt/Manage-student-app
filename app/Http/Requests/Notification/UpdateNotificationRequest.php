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
            'sender_id.required' => __('sender_id.required'),
            'sender_id.exists' => __('sender_id.not_existed'),
            'receiver_id.required' => __('receiver_id.required'),
            'notification_type.required' => __('notification_type.required'),
            'comment_text.string' => __('comment_text.must_be_string'),
            'title.required' => __('title.required'),
            'title.string' => __('title.string'),
            'message.required' => __('message.required'),
            'message.string' => __('message.string')
        ];
    }
}
