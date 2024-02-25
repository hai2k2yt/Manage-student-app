<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    protected array $sortFields = [
        'role_name'
    ];
    protected array $filterFields = [
        'role_name',
        'role_name_like'
    ];

    protected function getModel(): string
    {
        return Role::class;
    }
}
