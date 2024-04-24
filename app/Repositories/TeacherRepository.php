<?php

namespace App\Repositories;

use App\Models\Student;
use App\Models\Teacher;

class TeacherRepository extends BaseRepository
{
    protected array $sortFields = [
        'teacher_code',
        'teacher_name'
    ];
    protected array $filterFields = [
        'teacher_name',
        'teacher_name_like',
        'teacher_code'
    ];

    protected function getModel(): string
    {
        return Teacher::class;
    }

    public function getTeacherList(array $conditions)
    {
        $collection = $this->getCollections();

        return $this->applyConditions($collection, $conditions, ['*'], ['class']);
    }

    public function getTeacher(string $teacher_code) {
        return $this->model->where('teacher_code', $teacher_code)->first();
    }

    public function getTeacherByUserID(string $user_id) {
        return $this->model->where('user_id', $user_id)->first();
    }
}
