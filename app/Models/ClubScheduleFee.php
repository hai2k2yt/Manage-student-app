<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubScheduleFee extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_code',
        'club_teacher_fee',
        'class_teacher_fee'
    ];

    protected $table = 'club_schedule_fees';

    public function schedule(): BelongsTo {
        return $this->belongsTo(ClubSchedule::class, 'schedule_code', 'schedule_code');
    }

}
