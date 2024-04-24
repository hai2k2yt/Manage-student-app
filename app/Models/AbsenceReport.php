<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceReport extends BaseModel
{
    protected $table = 'absence_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_code',
        'student_code',
        'reason',
        'status',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClubSession::class, 'session_code', 'session_code');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_code', 'student_code');
    }
}
