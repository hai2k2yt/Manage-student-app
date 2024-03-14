<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClubNotification extends Notification
{
    use Queueable;

    protected string $sender_id;
    protected string $receiver_id;
    protected string $type;
    protected string $title;
    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $sender_id, string $receiver_id, string $type, string $title, string $message)
    {
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message
        ];
    }
}
