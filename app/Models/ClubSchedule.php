<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubSchedule extends BaseModel
{
    protected $table = 'club_schedules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time'
    ];

    public function club(): BelongsTo {
        return $this->belongsTo(Club::class);
    }

    public function sessions(): HasMany {
        return $this->hasMany(ClubSession::class, 'schedule_id');
    }
}
