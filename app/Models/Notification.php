<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends BaseModel
{
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'notification_type',
        'title',
        'message'
    ];

    public function receiver(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'notification_type', 'receiver_id');
    }
}
