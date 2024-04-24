<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubEnrollment extends Pivot
{
    protected $table = 'club_enrollments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_code',
        'club_code'
    ];
}
