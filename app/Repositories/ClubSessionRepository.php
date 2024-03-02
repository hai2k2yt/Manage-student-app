<?php

namespace App\Repositories;

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
        'date_lte'
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
}
