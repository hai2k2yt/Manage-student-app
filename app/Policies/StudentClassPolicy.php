<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\StudentClass;
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
        if ($user->role == RoleEnum::ADMIN) return true;
        if($user->role == RoleEnum::TEACHER && $user->id == $studentClass->teacher_id) return true;
        return false;
    }
}
