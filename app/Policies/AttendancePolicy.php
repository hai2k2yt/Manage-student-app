<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Models\ClubSession;
use App\Models\User;

class AttendancePolicy
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
     * @param ClubSession $clubSession
     * @return bool
     */
    public function store(User $user, ClubSession $clubSession): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            if ($user->id == $clubSession->teacher_id) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Attendance $attendance
     * @return bool
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value && $user->id == $attendance->session->teacher_id) return true;


        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Attendance $attendance
     * @return bool
     */
    public function destroy(User $user, Attendance $attendance): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value && $user->id == $attendance->session->teacher_id) return true;
        return false;
    }
}
