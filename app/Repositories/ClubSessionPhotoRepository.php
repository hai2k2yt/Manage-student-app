<?php

namespace App\Repositories;

use App\Models\ClubSession;
use App\Models\ClubSessionPhoto;

class ClubSessionPhotoRepository extends BaseRepository
{
    protected array $sortFields = [
        'created_at'
    ];

    protected array $filterFields = [];

    protected function getModel(): string
    {
        return ClubSessionPhoto::class;
    }
}
