<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
