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
        'schedule_id',
        'date'
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClubSchedule::class, 'schedule_id');
    }

    public function absence_reports(): HasMany
    {
        return $this->hasMany(AbsenceReport::class, 'session_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ClubSessionPhoto::class, 'session_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'session_id');
    }
}
