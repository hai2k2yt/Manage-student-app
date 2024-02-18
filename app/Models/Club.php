<?php

namespace App\Models;

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
}
