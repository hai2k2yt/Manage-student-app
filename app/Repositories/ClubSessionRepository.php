<?php

namespace App\Repositories;

use App\Models\ClubSchedule;
use App\Models\ClubSession;
use Illuminate\Database\Eloquent\Model;

class ClubSessionRepository extends BaseRepository
{
    protected array $sortFields = [
        'date'
    ];
    protected array $filterFields = [
        'date',
        'date_gte',
        'date_lte',
        'schedule_code'
    ];

    protected function getModel(): string
    {
        return ClubSession::class;
    }

    public function getClubSessionList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions,  ['*'], ['schedule.club', 'schedule.teacher']);
    }

    public function getClubSession(string $session_code)
    {
        return $this->model->where('session_code', $session_code)->first();
    }

    public function getByClubCode(string $id, array $conditions)
    {
        $club_schedule_codes = ClubSchedule::where('club_code', $id)->pluck('schedule_code')->toArray();

        $collection = $this->getCollections();

        return $this->applyConditions($collection, [...$conditions, 'schedule_code' => $club_schedule_codes], ['*'], ['schedule.club', 'schedule.teacher']);
    }
}
