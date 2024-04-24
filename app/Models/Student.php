<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends BaseModel
{
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_code',
        'name',
        'user_id',
        'class_code'
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_code', 'class_code');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_enrollments', 'student_code', 'club_code', 'student_code', 'club_code');
    }

    public function notification(): MorphOne
    {
        return $this->morphOne(Notification::class, 'receiver');
    }
}
