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
        'schedule_id'
    ];

    protected function getModel(): string
    {
        return ClubSession::class;
    }

    public function getClubSessionList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }

    public function getClubSession(string $id)
    {
        return $this->find($id);
    }

    public function getByClubId(string $id)
    {
        $club_schedule_ids = ClubSchedule::where('club_id', $id)->pluck('id')->toArray();

        $collection = $this->getCollections();

        return $this->applyConditions($collection, ['schedule_id' => $club_schedule_ids]);
    }
}
