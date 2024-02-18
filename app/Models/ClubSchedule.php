<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
