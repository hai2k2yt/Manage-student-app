<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\ClubSession;
use App\Models\Comment;
use App\Models\Teacher;
use App\Models\User;

class CommentPolicy
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
     * @param Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id);
            if (!$teacher) return false;
            if ($teacher->teacher_code == $comment->session->teacher_code) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function destroy(User $user, Comment $comment): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = Teacher::where('user_id', $user->id);
            if (!$teacher) return false;
            if ($teacher->teacher_code == $comment->session->teacher_code) return true;
        }
        return false;
    }
}
