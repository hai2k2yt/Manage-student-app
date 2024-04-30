<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\ClubSchedule;
use App\Models\ClubSession;

class AttendanceRepository extends BaseRepository
{
    protected array $sortFields = [
        'student_code',
        'present'
    ];

    protected array $filterFields = [
        'session_code',
        'student_code',
        'present'
    ];

    protected function getModel(): string
    {
        return Attendance::class;
    }

    public function getAttendanceList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['student']);
    }

    public function byClubStudent($student_code, $club_code) {
        $schedule_codes = ClubSchedule::where('club_code', $club_code)->pluck('schedule_code')->toArray();

        $session_codes = ClubSession::whereIn('schedule_code', $schedule_codes)->pluck('session_code')->toArray();
        return $this->getAllByConditions(['session_code' => $session_codes, 'student_code' => $student_code]);
    }
}
