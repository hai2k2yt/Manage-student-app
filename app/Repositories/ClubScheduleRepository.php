<?php

namespace App\Repositories;

use App\Models\ClubSchedule;

class ClubScheduleRepository extends BaseRepository
{
    protected array $sortFields = [];
    protected array $filterFields = [];

    protected function getModel(): string
    {
        return ClubSchedule::class;
    }

    public function getClubScheduleList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }
}
