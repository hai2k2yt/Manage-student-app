<?php

namespace App\Repositories;

class ClubRepository extends BaseRepository
{
    protected array $sortFields = [];
    protected array $filterFields = [];

    protected function getModel(): string
    {
        return ClubRepository::class;
    }
}
