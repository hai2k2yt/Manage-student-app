<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\AbsenceReport;
use App\Models\ClubSession;
use App\Models\Teacher;
use App\Models\User;
use App\Repositories\TeacherRepository;

class AbsenceReportPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct(
        protected TeacherRepository $teacherRepository
    )
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
        if ($user->role == RoleEnum::PARENT->value) return true;
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
        if ($user->role == RoleEnum::PARENT->value) return true;
        if ($user->role == RoleEnum::TEACHER->value) {
            $teacher = $this->teacherRepository->getTeacherByUserID($user->id);
            if (!$teacher) return false;
            if ($teacher->teacher_code == $absenceReport->session->schedule->teacher->teacher_code) return true;
        }
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
        if ($user->role == RoleEnum::TEACHER->value) return true;
        return false;
    }
}
