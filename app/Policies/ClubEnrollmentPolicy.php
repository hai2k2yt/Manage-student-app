<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Club;
use App\Models\ClubEnrollment;
use App\Models\Teacher;
use App\Models\User;

class ClubEnrollmentPolicy
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
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) return true;
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) return true;
        return false;
    }

    public function cancel(User $user, ClubEnrollment $enrollment): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $requestTeacher = Teacher::where('user_id', $user->id);
            $club = Club::where('club_code', $enrollment->club_code);
            if (!$requestTeacher || !$club) return false;
            if ($requestTeacher->teacher_code == $club->teacher_code) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function assignStudents(User $user): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) return true;
        return false;
    }
}
