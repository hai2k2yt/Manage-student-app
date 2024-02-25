<?php

namespace App\Repositories;

class AttendanceRepository extends BaseRepository
{
    protected array $sortFields = [];
    protected array $filterFields = [];

    protected function getModel(): string
    {
        return AttendanceRepository::class;
    }
}
