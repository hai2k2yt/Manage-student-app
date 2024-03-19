<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\ClubSchedule;
use App\Models\ClubSession;
use App\Models\User;

class ClubSessionPolicy
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
     * @param ClubSchedule $clubSchedule
     * @return bool
     */
    public function store(User $user, ClubSchedule $clubSchedule): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            if ($user->id == $clubSchedule->teacher_id) return true;
            if ($user->id == $clubSchedule->club->teacher_id) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ClubSession $clubSession
     * @return bool
     */
    public function update(User $user, ClubSession $clubSession): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value && $user->id == $clubSession->teacher_id) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ClubSession $clubSession
     * @return bool
     */
    public function destroy(User $user, ClubSession $clubSession): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value && $user->id == $clubSession->teacher_id) return true;
        return false;
    }
}
