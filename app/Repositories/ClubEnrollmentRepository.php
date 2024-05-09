<?php

namespace App\Repositories;


use App\Models\ClubEnrollment;

/**
 * Class ClubEnrollmentRepository.
 */
class ClubEnrollmentRepository extends BaseRepository
{
    protected array $sortFields = [

    ];

    protected array $filterFields = [
        'student_code',
        'club_code'
    ];

    /**
     * @return string
     *  Return the model
     */
    protected function getModel(): string
    {
        return ClubEnrollment::class;
    }

    public function getClubEnrollmentList(array $conditions)
    {
        return $this->getByConditions($conditions, ['*'], ['enrollment_histories', 'student']);
    }
}
