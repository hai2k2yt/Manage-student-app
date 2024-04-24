<?php

namespace App\Repositories;

use App\Models\Club;

class ClubRepository extends BaseRepository
{
    protected array $sortFields = [
        'club_code',
        'name'
    ];
    protected array $filterFields = [
        'club_code_like',
        'name_like',
        'teacher_code'
    ];

    protected function getModel(): string
    {
        return Club::class;
    }

    public function getClubList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['teacher']);
    }

    public function getClub(string $club_code)
    {
        return $this->model->where('club_code', $club_code)->first();
    }
}
