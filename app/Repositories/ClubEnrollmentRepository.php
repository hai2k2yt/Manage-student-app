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
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions);
    }
}
