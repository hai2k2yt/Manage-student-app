<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends BaseModel
{
    protected $table = 'attendances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_code',
        'student_code',
        'present'
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
