<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\AbsenceReport;
use App\Models\Attendance;
use App\Models\Club;
use App\Models\ClubEnrollment;
use App\Models\ClubSchedule;
use App\Models\ClubSession;
use App\Models\Comment;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Policies\AbsenceReportPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\ClubEnrollmentPolicy;
use App\Policies\ClubPolicy;
use App\Policies\ClubSchedulePolicy;
use App\Policies\ClubSessionPolicy;
use App\Policies\CommentPolicy;
use App\Policies\StudentClassPolicy;
use App\Policies\StudentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Student::class => StudentPolicy::class,
        StudentClass::class => StudentClassPolicy::class,
        Club::class => ClubPolicy::class,
        ClubEnrollment::class => ClubEnrollmentPolicy::class,
        ClubSchedule::class => ClubSchedulePolicy::class,
        ClubSession::class => ClubSessionPolicy::class,
        AbsenceReport::class => AbsenceReportPolicy::class,
        Attendance::class => AttendancePolicy::class,
        Comment::class => CommentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
