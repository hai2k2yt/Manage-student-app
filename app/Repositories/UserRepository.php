<?php

namespace App\Repositories;


use App\Models\User;

class UserRepository extends BaseRepository
{
    protected array $sortFields = [
        'name',
        'email'
    ];
    protected array $filterFields = [
        'name',
        'name_like',
        'email',
        'email_like'
    ];

    protected function getModel(): string
    {
        return User::class;
    }
}
