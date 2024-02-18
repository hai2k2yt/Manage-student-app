<?php

namespace App\Models;

class ClubEnrollment extends BaseModel
{
    protected $table = 'club_enrollment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'club_id'
    ];
}
