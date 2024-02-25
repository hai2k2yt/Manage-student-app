<?php

namespace App\Repositories;

use App\Models\Statistic;

class StatisticRepository extends BaseRepository
{
    protected array $sortFields = [];
    protected array $filterFields = [];

    protected function getModel(): string
    {
        return Statistic::class;
    }
}
