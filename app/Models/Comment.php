<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends BaseModel
{
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_session_id',
        'student_id',
        'comment_text',
        'rating'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClubSession::class);
    }
}
