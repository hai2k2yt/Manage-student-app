<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Club extends BaseModel
{
    protected $table = 'clubs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_code',
        'name',
        'teacher_code'
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'club_enrollments', 'club_code', 'student_code', 'club_code', 'student_code');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_code', 'teacher_code');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClubSchedule::class, 'club_code', 'club_code');
    }

    public function notification(): MorphOne {
        return $this->morphOne(Notification::class, 'receiver');
    }
}
