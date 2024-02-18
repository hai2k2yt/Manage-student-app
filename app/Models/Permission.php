<?php

namespace App\Models;

class Permission extends BaseModel
{
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permission_name',
    ];
}
