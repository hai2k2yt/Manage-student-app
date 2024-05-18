<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceEnum;
use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Requests\Attendance\UpdateManyAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Repositories\AttendanceRepository;
use App\Repositories\ClubRepository;
use App\Repositories\ClubSessionRepository;
use App\Repositories\TeacherRepository;
use Exception;
use HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{
    /**
     * @param AttendanceRepository $attendanceRepository
     * @param ClubSessionRepository $clubSessionRepository
     * @param ClubRepository $clubRepository
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(
        protected AttendanceRepository  $attendanceRepository,
        protected ClubSessionRepository $clubSessionRepository,
        protected ClubRepository        $clubRepository,
        protected TeacherRepository     $teacherRepository
    )
    {
    }

    public function all(): JsonResponse
    {
        $attendances = $this->attendanceRepository->getAll();
        $records = AttendanceResource::collection($attendances);
        return $this->sendResponse($records);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $conditions = $request->all();
        $attendances = $this->attendanceRepository->getAttendanceList($conditions);
        return $this->sendPaginationResponse($attendances, AttendanceResource::collection($attendances));
    }

    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $session_code = $requestData['session_code'];
            $student_code = $requestData['student_code'];
            $club_session = $this->clubSessionRepository->getClubSession($session_code);
            $club_student_codes = $club_session->schedule->club->students->pluck('student_code')->toArray();
            if (!in_array($student_code, $club_student_codes)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceStore,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['student' => __('student.error.not_in_club')]
                );
            }
            $attendance_student_codes = $club_session->attendance->pluck('student_code')->toArray();
            if (in_array($student_code, $attendance_student_codes)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceStore,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['attendance' => __('attendance.error.existed')]
                );
            }
            if ($request->user()->cannot('store', Attendance::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceStore,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club_session->schedule->teacher_code != $requestTeacher->teacher_code) {
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::AttendanceStore,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
                }
            }
            $attendance = $this->attendanceRepository->create($requestData);
            $attendanceResource = new AttendanceResource($attendance);
            DB::commit();
            return $this->sendResponse($attendanceResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateAttendanceRequest $request
     * @return JsonResponse
     */
    public function update(UpdateAttendanceRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $attendance = $this->attendanceRepository->find($id);
            if (!$attendance) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['attendance' => __('attendance.error.not_found')]
                );
            }
            if ($request->user()->cannot('update', $attendance)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceUpdate,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $attendance->session->schedule->teacher_code != $requestTeacher->teacher_code) {
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::AttendanceUpdate,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
                }
            }
            $attendance = $this->attendanceRepository->update($id, $requestData);
            $attendanceResource = new AttendanceResource($attendance);
            DB::commit();
            return $this->sendResponse($attendanceResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceUpdate);
        }
    }

    public function updateMany(UpdateManyAttendanceRequest $request, string $id): JsonResponse
    {
        if (!$request->user()) {
            return $this->sendError(
                null,
                ErrorCodeEnum::AttendanceUpdateMany,
                Response::HTTP_UNAUTHORIZED,
                ['auth' => __('common.unauthorized')]
            );
        }
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $present = $requestData['present'];
            $permission_absence = $requestData['permission_absence'];
            $unexcused_absence = $requestData['unexcused_absence'];
            $session = $this->clubSessionRepository->getClubSession($id);
            if (!$session) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceUpdateMany,
                    Response::HTTP_NOT_FOUND,
                    ['session' => __('session.not_found')]
                );
            }
            $session_student_codes = $session->schedule->club->students->pluck('student_code')->toArray();
            if ($request->user()->cannot('updateMany', Attendance::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceUpdateMany,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $session->schedule->teacher_id != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::AttendanceUpdateMany,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $error_student_code = [];
            foreach ($present as $student_code) {
                if (!in_array($student_code, $session_student_codes)) {
                    $error_student_code[] = $student_code;
                    continue;
                }
                $this->attendanceRepository->upsert(
                    ['session_code' => $id, 'student_code' => $student_code, 'present' => AttendanceEnum::PRESENT->value],
                    ['session_code', 'student_code'],
                    ['present']);
            }
            foreach ($permission_absence as $student_code) {
                if (!in_array($student_code, $session_student_codes)) {
                    $error_student_code[] = $student_code;
                    continue;
                }
                $this->attendanceRepository->upsert(
                    ['session_code' => $id, 'student_code' => $student_code, 'present' => AttendanceEnum::PERMISSION_ABSENCE->value],
                    ['session_code', 'student_code'],
                    ['present']);
            }
            foreach ($unexcused_absence as $student_code) {
                if (!in_array($student_code, $session_student_codes)) {
                    $error_student_code[] = $student_code;
                    continue;
                }
                $this->attendanceRepository->upsert(
                    ['session_code' => $id, 'student_code' => $student_code, 'present' => AttendanceEnum::UNEXCUSED_ABSENCE],
                    ['session_code', 'student_code'],
                    ['present']);
            }
            if (count($error_student_code)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceUpdateMany,
                    Response::HTTP_NOT_FOUND,
                    ['attendance' => __('attendance.error.student', $error_student_code)]
                );
            }
            DB::commit();
            return $this->sendResponse('', __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceUpdateMany);
        }
    }

    /**
     * Delete corporation department.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $attendance = $this->attendanceRepository->find($id);
            if (!$attendance) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceDelete,
                    Response::HTTP_NOT_FOUND,
                    ['attendance' => __('attendance.error.not_found')]
                );
            }
            if ($request->user()->cannot('destroy', $attendance)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceDelete,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $attendance->session->schedule->teacher_id != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::AttendanceDelete,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $this->attendanceRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceDelete);
        }
    }

    public function statisticStudents(string $club_code, string $student_code): JsonResponse
    {
        try {
            $club = $this->clubRepository->getClub($club_code);
            if (!$club) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceStatisticStudent,
                    Response::HTTP_NOT_FOUND,
                    ['club' => __('club.error.not_found')]
                );
            }
            $session_codes = [];
            $club->schedules->each(function ($schedule) use (&$session_codes) {
                $schedule->sessions->each(function ($session) use (&$session_codes) {
                    $session_codes[] = $session->session_code;
                });
            });

            $club_student_codes = $club->students->pluck('student_code')->toArray();
            if (!in_array($student_code, $club_student_codes)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::AttendanceStatisticStudent,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['club' => __('club.error.no_student')]
                );
            }

            $attendances = $this->attendanceRepository->getAttendanceList(
                [
                    'session_code' => $session_codes,
                    'student_code' => $student_code
                ]
            )->pluck('present')->toArray();

            $statistics = array_count_values($attendances);

            return $this->sendResponse($statistics, __('common.get_success'));
        } catch (Exception $error) {
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceStatisticStudent);
        }
    }

    public function getClubStudent(Request $request): JsonResponse
    {
        $res = $request->all();
        $student_code = $res['student_code'];
        $club_code = $res['club_code'];

        $attendances = $this->attendanceRepository->byClubStudent($student_code, $club_code);
        return $this->sendResponse($attendances);
    }
}
