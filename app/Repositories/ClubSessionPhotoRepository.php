<?php

namespace App\Repositories;

use App\Models\ClubSession;
use App\Models\ClubSessionPhoto;

class ClubSessionPhotoRepository extends BaseRepository
{
    protected array $sortFields = [
        'created_at'
    ];

    protected array $filterFields = [
        'session_id'
    ];

    protected function getModel(): string
    {
        return ClubSessionPhoto::class;
    }

    public function getClubSessionPhotoList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }
}
