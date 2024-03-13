<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

class Club extends BaseModel
{
    protected $table = 'clubs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'teacher_id'
    ];

    public function notification(): MorphOne {
        return $this->morphOne(Notification::class, 'receiver');
    }
}
