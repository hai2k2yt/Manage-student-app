<?php

namespace App\Repositories;

use App\Models\ClubSchedule;

class ClubScheduleRepository extends BaseRepository
{
    protected array $sortFields = [
        'schedule_code',
        'day_of_week'
    ];
    protected array $filterFields = [
        'club_code',
        'teacher_code',
        'day_of_week'
    ];

    protected function getModel(): string
    {
        return ClubSchedule::class;
    }

    public function getClubScheduleList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['club', 'teacher']);
    }

    public function getClubSchedule(string $schedule_code)
    {
        return $this->model->where('schedule_code', $schedule_code)->first();
    }
}
