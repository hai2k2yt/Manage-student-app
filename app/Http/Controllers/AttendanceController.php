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
use App\Repositories\AttendanceRepository;
use App\Repositories\ClubRepository;
use App\Repositories\ClubSessionRepository;
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
     */
    public function __construct(
        protected AttendanceRepository  $attendanceRepository,
        protected ClubSessionRepository $clubSessionRepository,
        protected ClubRepository        $clubRepository
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
            $club_session_id = $requestData['club_session_id'];
            $student_id = $requestData['student_id'];
            $club_session = $this->clubSessionRepository->find($club_session_id);
            $club_student_ids = $club_session->schedule->club->students->pluck('id')->toArray();
            if (!in_array($student_id, $club_student_ids)) {
                return $this->sendError(__('student.existed'), ErrorCodeEnum::AttendanceStore);
            }
            $attendance_student_ids = $club_session->attendance->pluck('id')->toArray();
            if (in_array($student_id, $attendance_student_ids)) {
                return $this->sendError(__('attendance.existed'), ErrorCodeEnum::AttendanceStore);
            }
            if ($request->user()->cannot('store', Attendance::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club_session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
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
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::AttendanceUpdate, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('update', Attendance::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $attendance->session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
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
            return $this->sendError(__('common.unauthorized'), ErrorCodeEnum::AttendanceUpdateMany, Response::HTTP_UNAUTHORIZED);
        }
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $present = $requestData['present'];
            $permission_absence = $requestData['permission_absence'];
            $unexcused_absence = $requestData['unexcused_absence'];
            $session = $this->clubSessionRepository->find($id);
            if (!$session) {
                return $this->sendError(__('session.not_found'), ErrorCodeEnum::AttendanceUpdateMany, Response::HTTP_NOT_FOUND);
            }
            $session_student_ids = $session->schedule->club->students->pluck('id')->toArray();
            if ($request->user()->cannot('updateMany', Attendance::class)) {
                return $this->sendError(__('common.forbidden'), ErrorCodeEnum::AttendanceUpdateMany, Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $session->schedule->teacher_id != $request->user()->id) {
                return $this->sendError(__('common.forbidden'), ErrorCodeEnum::AttendanceUpdateMany, Response::HTTP_FORBIDDEN);
            }
            $error_student_id = [];
            foreach ($present as $student_id) {
                if (!in_array($student_id, $session_student_ids)) {
                    $error_student_id[] = $student_id;
                    continue;
                }
                $this->attendanceRepository->upsert(
                    ['club_session_id' => $id, 'student_id' => $student_id, 'present' => AttendanceEnum::PRESENT->value],
                    ['club_session_id', 'student_id'],
                    ['present']);
            }
            foreach ($permission_absence as $student_id) {
                if (!in_array($student_id, $session_student_ids)) {
                    $error_student_id[] = $student_id;
                    continue;
                }
                $this->attendanceRepository->upsert(
                    ['club_session_id' => $id, 'student_id' => $student_id, 'present' => AttendanceEnum::PERMISSION_ABSENCE->value],
                    ['club_session_id', 'student_id'],
                    ['present']);
            }
            foreach ($unexcused_absence as $student_id) {
                if (!in_array($student_id, $session_student_ids)) {
                    $error_student_id[] = $student_id;
                    continue;
                }
                $this->attendanceRepository->upsert(
                    ['club_session_id' => $id, 'student_id' => $student_id, 'present' => AttendanceEnum::UNEXCUSED_ABSENCE],
                    ['club_session_id', 'student_id'],
                    ['present']);
            }
            if (count($error_student_id)) {
                return $this->sendError(__('attendance.not_found'), ErrorCodeEnum::AttendanceUpdateMany, Response::HTTP_NOT_FOUND, $error_student_id);
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
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::AttendanceDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', Attendance::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $attendance->session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $this->attendanceRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceDelete);
        }
    }

    public function statisticStudents(string $club_id, string $student_id): JsonResponse
    {
        try {
            $club = $this->clubRepository->find($club_id);
            if (!$club) {
                return $this->sendError(__('club.not_found'), ErrorCodeEnum::AttendanceStatisticStudent, Response::HTTP_NOT_FOUND);
            }
            $session_ids = [];
            $club->schedules->each(function ($schedule) use (&$session_ids) {
                $schedule->sessions->each(function ($session) use (&$session_ids) {
                    $session_ids[] = $session->id;
                });
            });

            $club_student_ids = $club->students->pluck('id')->toArray();
            if (!in_array($student_id, $club_student_ids)) {
                return $this->sendError(__('club.not_have_student'), ErrorCodeEnum::AttendanceStatisticStudent, Response::HTTP_NOT_FOUND);
            }

            $attendances = $this->attendanceRepository->getAttendanceList(
                [
                    'club_session_id' => $session_ids,
                    'student_id' => $student_id
                ]
            )->pluck('present')->toArray();

            $statistics = array_count_values($attendances);

            return $this->sendResponse($statistics, __('common.get_success'));
        } catch (Exception $error) {
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceStatisticStudent);
        }
    }
}
