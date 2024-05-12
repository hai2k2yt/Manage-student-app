<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Club;
use App\Models\Teacher;
use App\Models\User;

class ClubPolicy
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
     * Determine whether the user can create the model.
     *
     * @param User $user
     * @return bool
     */
    public function me(User $user): bool
    {
        return $user->role == RoleEnum::TEACHER->value;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Club $club
     * @return bool
     */
    public function update(User $user, Club $club): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id);
            if (!$teacher) return false;
            if ($teacher->teacher_code == $club->teacher_code) return true;
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
        return $user->role == RoleEnum::ADMIN->value;
    }
}
