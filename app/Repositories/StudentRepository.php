<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository extends BaseRepository
{
    protected array $sortFields = [
        'name'
    ];
    protected array $filterFields = [
        'name',
        'name_like',
        'user_id',
        'class_id'
    ];

    protected function getModel(): string
    {
        return Student::class;
    }

    public function getStudentList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['clubs', 'class']);
    }

    public function getStudent(string $id) {
        return $this->find($id, ['clubs', 'class']);
    }
}
