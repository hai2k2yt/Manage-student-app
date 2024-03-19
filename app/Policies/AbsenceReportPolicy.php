<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\AbsenceReport;
use App\Models\ClubSession;
use App\Models\User;

class AbsenceReportPolicy
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
     * @param AbsenceReport $absenceReport
     * @return bool
     */
    public function update(User $user, AbsenceReport $absenceReport): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value && $user->id == $absenceReport->session->teacher_id) return true;


        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param AbsenceReport $absenceReport
     * @return bool
     */
    public function destroy(User $user, AbsenceReport $absenceReport): bool
    {
        if ($user->role == RoleEnum::ADMIN->value) return true;
        if ($user->role == RoleEnum::TEACHER->value && $user->id == $absenceReport->session->teacher_id) return true;
        return false;
    }
}
