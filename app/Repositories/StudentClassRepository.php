<?php

namespace App\Repositories;

use App\Models\StudentClass;

class StudentClassRepository extends BaseRepository
{
    protected array $sortFields = [
        'class_name'
    ];
    protected array $filterFields = [
        'class_name_like'
    ];

    protected function getModel(): string
    {
        return StudentClass::class;
    }

    public function getStudentClassList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['students']);
    }

    public function getStudentClass(string $id)
    {
        return $this->find($id, ['students']);
    }
}
