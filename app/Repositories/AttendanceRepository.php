<?php

namespace App\Repositories;

use App\Models\Attendance;

class AttendanceRepository extends BaseRepository
{
    protected array $sortFields = [];
    protected array $filterFields = [];

    protected function getModel(): string
    {
        return Attendance::class;
    }

    public function getAttendanceList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }
}
