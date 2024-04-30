<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\Statistic\StatisticStudentFeeRequest;
use App\Http\Requests\Statistic\StatisticTeacherFeeRequest;
use App\Models\Club;
use App\Models\ClubEnrollment;
use App\Models\ClubScheduleFee;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatisticController extends Controller
{
    public function statisticStudentFee(StatisticStudentFeeRequest $request): JsonResponse
    {
        if ($request->user()->role != RoleEnum::ADMIN->value && $request->user()->role != RoleEnum::ACCOUNTANT->value) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }
        $requestData = $request->validated();
        $from = strtotime($requestData['from']);
        $to = strtotime($requestData['to']) + 86400;
        $students = Student::all();
        $clubs = Club::all();
        $fee_table = [];
        foreach ($clubs as $club) {
            foreach ($students as $student) {
                $fee_table[$club->club_code][$student->student_code] = 0;
            }
        }
        foreach ($clubs as $club) {
            foreach ($students as $student) {
                $enrollment = ClubEnrollment::where('club_code', $club->club_code)
                    ->where('student_code', $student->student_code)
                    ->first();
                if (!$enrollment) continue;

                $enrollment_histories = $enrollment->enrollment_histories;
                $schedules = $club->schedules;
                foreach ($schedules as $schedule) {
                    $schedule_fee = ClubScheduleFee::where('schedule_code', $schedule->schedule_code)->first()->student_fee;
                    $sessions = $schedule->sessions;
                    $filtered_sessions = $sessions->filter(function ($item) use ($to, $from, $enrollment_histories) {
                        $sessionDate = strtotime($item->date);
                        if ($sessionDate < $from || $sessionDate > $to) return false;
                        foreach ($enrollment_histories as $history) {
                            $fromDate = strtotime($history->from);
                            $toDate = strtotime($history->to);
                            if ($sessionDate >= $fromDate) {
                                if (!$toDate || ($sessionDate <= $toDate)) return true;
                            }
                        }
                        return false;
                    })->count();
                    $fee_table[$club->club_code][$student->student_code] += intval($filtered_sessions) * intval($schedule_fee);
                }
            }
        }
        return $this->sendResponse($fee_table, __('common.get_success'));
    }

    public function statisticTeacherFee(StatisticTeacherFeeRequest $request): JsonResponse
    {
        if ($request->user()->role != RoleEnum::ADMIN->value && $request->user()->role != RoleEnum::ACCOUNTANT->value) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }
        $requestData = $request->validated();
        $from = strtotime($requestData['from']);
        $to = strtotime($requestData['to']) + 86400;
        $teachers = Teacher::all();
        $clubs = Club::all();
        $fee_table = [];
        foreach ($clubs as $club) {
            foreach ($teachers as $teacher) {
                $fee_table[$club->club_code][$teacher->teacher_code] = 0;
            }
        }
        foreach ($clubs as $club) {
            $schedules = $club->schedules;
            foreach ($schedules as $schedule) {
                $teacher = $schedule->teacher;
                $schedule_fee = ClubScheduleFee::where('schedule_code', $schedule->schedule_code)->first()->teacher_fee;
                $sessions = $schedule->sessions;
                $filtered_sessions = $sessions->filter(function ($item) use ($to, $from) {
                    $sessionDate = strtotime($item->date);
                    if ($sessionDate < $from || $sessionDate > $to) return false;
                    return true;
                })->count();
                $fee_table[$club->club_code][$teacher->teacher_code] += intval($filtered_sessions) * intval($schedule_fee);
            }
        }
        return $this->sendResponse($fee_table, __('common.get_success'));
    }


}
