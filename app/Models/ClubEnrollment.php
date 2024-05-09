<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubEnrollment extends Pivot
{
    use HasFactory;

    protected $table = 'club_enrollments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_code',
        'club_code',
        'status'
    ];

    public function enrollment_histories(): HasMany {
        return $this->hasMany(ClubEnrollmentHistory::class, 'club_enrollment_id', 'id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }

    public function club(): BelongsTo {
        return $this->belongsTo(Club::class, 'club_code', 'club_code');
    }
}
