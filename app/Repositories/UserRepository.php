<?php

namespace App\Repositories;


use App\Models\UserModel;

class UserRepository extends BaseRepository
{
    protected array $sortFields = [
        'name',
        'username',
        'role'
    ];
    protected array $filterFields = [
        'name',
        'name_like',
        'username',
        'username_like',
        'role'
    ];

    protected function getModel(): string
    {
        return UserModel::class;
    }

    public function getUserList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }
}
