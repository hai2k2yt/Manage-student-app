<?php

namespace App\Repositories;


use App\Models\Notification;

class NotificationRepository extends BaseRepository
{
    protected array $sortFields = [
        'notification_type'
    ];
    protected array $filterFields = [
        'notification_type'
    ];

    protected function getModel(): string
    {
        return Notification::class;
    }
}
