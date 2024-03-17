<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'name',
        'user_id',
        'class_id'
    ];

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_enrollment', 'student_id', 'club_id');
    }

    public function notification(): MorphOne
    {
        return $this->morphOne(Notification::class, 'receiver');
    }
}
