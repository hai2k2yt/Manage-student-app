<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\ClubSession;
use App\Models\ClubSessionPhoto;
use App\Models\Teacher;
use App\Models\User;

class ClubSessionPhotoPolicy
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
     * @param ClubSessionPhoto $clubSessionPhoto
     * @return bool
     */
    public function update(User $user, ClubSessionPhoto $clubSessionPhoto): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ClubSessionPhoto $clubSessionPhoto
     * @return bool
     */
    public function destroy(User $user, ClubSessionPhoto $clubSessionPhoto): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id);
            if (!$teacher) return false;
            if ($teacher->teacher_code == $clubSessionPhoto->session->teacher_code) return true;
        }
        return false;
    }
}
