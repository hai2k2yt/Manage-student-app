<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubSessionPhoto extends BaseModel
{
    protected $table = 'club_session_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_code',
        'photo_url'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClubSession::class, 'session_code', 'session_code');
    }
}
