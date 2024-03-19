<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends BaseModel
{
    protected $table = 'attendances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_session_id',
        'student_id',
        'present'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClubSession::class);
    }
}
