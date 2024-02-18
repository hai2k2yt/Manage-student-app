<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceReport extends BaseModel
{
    protected $table = 'absence_report';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_session_id',
        'student_id',
        'reason',
        'status',
    ];
}
