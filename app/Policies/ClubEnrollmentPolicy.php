<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Club;
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
    public function store(User $user, Club $club): bool
    {
        if($user->role == RoleEnum::ADMIN->value) return true;
        if($user->role == RoleEnum::TEACHER->value && $user->id == $club->teacher_id) return true;
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user, Club $club): bool
    {
        if($user->role == RoleEnum::ADMIN->value) return true;
        if($user->role == RoleEnum::TEACHER->value && $user->id == $club->teacher_id) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function destroy(User $user, Club $club): bool
    {
        if($user->role == RoleEnum::ADMIN->value) return true;
        if($user->role == RoleEnum::TEACHER->value && $user->id == $club->teacher_id) return true;
        return false;
    }
}
