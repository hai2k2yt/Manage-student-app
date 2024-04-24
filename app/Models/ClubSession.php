<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubSession extends BaseModel
{
    protected $table = 'club_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_code',
        'session_name',
        'schedule_code',
        'date'
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClubSchedule::class, 'schedule_code', 'schedule_code');
    }

    public function absence_reports(): HasMany
    {
        return $this->hasMany(AbsenceReport::class, 'session_code', 'session_code');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_code', 'session_code');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ClubSessionPhoto::class, 'session_code', 'session_code');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'session_code', 'session_code');
    }
}
