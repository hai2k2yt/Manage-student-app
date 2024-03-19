<?php

namespace App\Repositories;

use App\Models\Attendance;

class AttendanceRepository extends BaseRepository
{
    protected array $sortFields = [
        'present'
    ];

    protected array $filterFields = [
        'club_session_id',
        'student_id',
        'present'
    ];

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
