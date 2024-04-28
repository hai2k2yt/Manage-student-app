<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubEnrollmentHistory extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_enrollment_id',
        'from',
        'to',
        'status',
    ];

    protected $table = 'club_enrollment_histories';

    public function club_enrollment(): BelongsTo {
        return $this->belongsTo(ClubEnrollment::class, 'club_enrollment_id', 'id');
    }
}
