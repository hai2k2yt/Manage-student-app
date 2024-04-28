<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClubSchedule extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_code',
        'schedule_name',
        'club_code',
        'teacher_code',
        'day_of_week'
    ];

    protected $table = 'club_schedules';

    public function club(): BelongsTo {
        return $this->belongsTo(Club::class, 'club_code', 'club_code');
    }

    public function teacher(): BelongsTo {
        return $this->belongsTo(Teacher::class, 'teacher_code', 'teacher_code');
    }

    public function sessions(): HasMany {
        return $this->hasMany(ClubSession::class, 'schedule_code', 'schedule_code');
    }

    public function fee(): HasOne {
        return $this->hasOne(ClubScheduleFee::class, 'schedule_code', 'schedule_code');
    }
}
