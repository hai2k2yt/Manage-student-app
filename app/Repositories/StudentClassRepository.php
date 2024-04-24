<?php

namespace App\Repositories;

use App\Models\StudentClass;

class StudentClassRepository extends BaseRepository
{
    protected array $sortFields = [
        'class_code',
        'class_name',
    ];
    protected array $filterFields = [
        'class_code',
        'class_name_like',
    ];

    protected function getModel(): string
    {
        return StudentClass::class;
    }

    public function getStudentClassList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['teacher']);
    }

    public function getStudentClass(string $class_code)
    {
        return $this->model->where('class_code', $class_code)->first();

    }
}
