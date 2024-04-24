<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;

class StudentClassPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can create the model.
     *
     * @param User $user
     * @return bool
     */
    public function store(User $user): bool
    {
        return $user->role == RoleEnum::ADMIN->value;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->role == RoleEnum::ADMIN->value;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $user->role == RoleEnum::ADMIN->value;
    }

    public function assignStudents(User $user, StudentClass $studentClass) {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id);
            if (!$teacher) return false;
            if ($teacher->teacher_code == $studentClass->teacher_code) return true;
        }
        return false;
    }
}
