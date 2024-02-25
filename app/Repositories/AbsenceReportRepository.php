<?php

namespace App\Repositories;

use App\Models\AbsenceReport;

class AbsenceReportRepository extends BaseRepository
{
    protected array $sortFields = [];
    protected array $filterFields = [];

    protected function getModel(): string
    {
        return AbsenceReport::class;
    }
}
