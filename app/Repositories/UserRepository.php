<?php

namespace App\Repositories;


use App\Models\User;

class UserRepository extends BaseRepository
{
    protected array $sortFields = [
        'name',
        'username'
    ];
    protected array $filterFields = [
        'name',
        'name_like',
        'username',
        'username_like'
    ];

    protected function getModel(): string
    {
        return User::class;
    }
}
