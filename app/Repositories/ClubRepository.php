<?php

namespace App\Repositories;

use App\Models\Club;

class ClubRepository extends BaseRepository
{
    protected array $sortFields = [
        'name'
    ];
    protected array $filterFields = [
        'name_like',
        'teacher_id'
    ];

    protected function getModel(): string
    {
        return Club::class;
    }

    public function getClubList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }

    public function getClub(string $id)
    {
        return $this->find($id, ['students']);
    }
}
