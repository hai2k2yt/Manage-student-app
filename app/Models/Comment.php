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
        'session_code',
        'student_code',
        'content'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClubSession::class, 'session_code', 'session_code');
    }
}
