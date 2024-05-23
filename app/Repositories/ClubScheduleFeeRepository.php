<?php

namespace App\Repositories;

use App\Models\ClubSchedule;
use App\Models\ClubScheduleFee;

class ClubScheduleFeeRepository extends BaseRepository
{
    protected array $sortFields = [
        'student_fee',
        'teacher_fee'
    ];
    protected array $filterFields = [
        'schedule_code',
        'student_fee',
        'teacher_fee'
    ];

    protected function getModel(): string
    {
        return ClubScheduleFee::class;
    }

    public function getClubScheduleFee(string $schedule_code)
    {
        return $this->model->where('schedule_code', $schedule_code)->first();
    }
}
