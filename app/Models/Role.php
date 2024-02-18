<?php

namespace App\Models;



class Role extends BaseModel
{
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_name',
    ];
}
