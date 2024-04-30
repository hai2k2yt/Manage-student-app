<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository extends BaseRepository
{
    protected array $sortFields = [
        'student_code',
        'name'
    ];
    protected array $filterFields = [
        'name',
        'name_like',
        'user_id',
        'class_code'
    ];

    protected function getModel(): string
    {
        return Student::class;
    }

    public function getStudentList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['class', 'parent']);
    }

    public function getStudentByParent(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['class', 'clubs.schedules.sessions.absence_reports', 'clubs.schedules.sessions.attendances', 'clubs.schedules.sessions.comments']);
    }

    public function getStudent(string $student_code) {
        return $this->model->where('student_code', $student_code)->with(['clubs.schedules.sessions'])->first();

    }
}
