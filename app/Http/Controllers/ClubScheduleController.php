<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubSchedule\StoreClubScheduleRequest;
use App\Http\Requests\ClubSchedule\UpdateClubScheduleRequest;
use App\Http\Resources\ClubScheduleFeeResource;
use App\Http\Resources\ClubScheduleResource;
use App\Models\ClubSchedule;
use App\Repositories\ClubRepository;
use App\Repositories\ClubScheduleFeeRepository;
use App\Repositories\ClubScheduleRepository;
use App\Repositories\TeacherRepository;
use Exception;
use HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ClubScheduleController extends Controller
{
    /**
     * @param ClubScheduleRepository $clubScheduleRepository
     * @param ClubRepository $clubRepository
     * @param TeacherRepository $teacherRepository
     * @param ClubScheduleFeeRepository $clubScheduleFeeRepository
     */
    public function __construct(
        protected ClubScheduleRepository    $clubScheduleRepository,
        protected ClubRepository            $clubRepository,
        protected TeacherRepository         $teacherRepository,
        protected ClubScheduleFeeRepository $clubScheduleFeeRepository
    )
    {
    }

    public function all(): JsonResponse
    {
        $clubSchedules = $this->clubScheduleRepository->getAll();
        $records = ClubScheduleResource::collection($clubSchedules);
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
        $clubSchedules = $this->clubScheduleRepository->getClubScheduleList($conditions);
        return $this->sendPaginationResponse($clubSchedules, ClubScheduleResource::collection($clubSchedules));
    }

    public function store(StoreClubScheduleRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $scheduleData = [
                ...(isset($requestData['club_code']) ? ['club_code' => $requestData['club_code'] ] : []),
                ...(isset($requestData['schedule_code']) ? ['schedule_code' => $requestData['schedule_code'] ] : []),
                ...(isset($requestData['schedule_name']) ? ['schedule_name' => $requestData['schedule_name'] ] : []),
                ...(isset($requestData['teacher_code']) ? ['teacher_code' => $requestData['teacher_code'] ] : []),
                ...(isset($requestData['day_of_week']) ? ['day_of_week' => $requestData['day_of_week'] ] : []),
            ];
            $feeData = [
                ...(isset($requestData['schedule_code']) ? ['schedule_code' => $requestData['schedule_code'] ] : []),
                ...(isset($requestData['student_fee']) ? ['student_fee' => $requestData['student_fee'] ] : []),
                ...(isset($requestData['teacher_fee']) ? ['teacher_fee' => $requestData['teacher_fee'] ] : []),
            ];
            $club_code = $requestData['club_code'];
            $club = $this->clubRepository->getClub($club_code);
            if ($request->user()->cannot('store', ClubSchedule::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubScheduleStore,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubScheduleStore,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $clubSchedule = $this->clubScheduleRepository->create($scheduleData);

            $clubScheduleFee = $this->clubScheduleFeeRepository->create($feeData);

            $clubScheduleResource = [...$clubSchedule->toArray(), ...$clubScheduleFee->toArray()];
            DB::commit();
            return $this->sendResponse($clubScheduleResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubScheduleStore);
        }
    }

    public function show(string $id): JsonResponse
    {
        $clubSchedule = $this->clubScheduleRepository->getClubSchedule($id);
        $clubScheduleFee = $this->clubScheduleFeeRepository->getClubScheduleFee($id);
        if (!$clubSchedule) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubScheduleShow,
                Response::HTTP_NOT_FOUND,
                ['club_schedule' => __('club_schedule.error.not_found')]
            );
        }
        if (!$clubScheduleFee) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubScheduleShow,
                Response::HTTP_NOT_FOUND,
                ['club_schedule' => __('club_schedule.error.not_found_fee')]
            );
        }
        return $this->sendResponse([...$clubSchedule->toArray(), ...$clubScheduleFee->toArray()]);
    }

    public function getByClub(string $id): JsonResponse
    {
        $clubSchedules = $this->clubScheduleRepository->getClubScheduleList(array(
            'club_code' => $id
        ));
        return $this->sendResponse($clubSchedules);
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateClubScheduleRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClubScheduleRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $scheduleData = [
                ...(isset($requestData['schedule_name']) ? ['schedule_name' => $requestData['schedule_name'] ] : []),
                ...(isset($requestData['teacher_code']) ? ['teacher_code' => $requestData['teacher_code'] ] : []),
                ...(isset($requestData['day_of_week']) ? ['day_of_week' => $requestData['day_of_week'] ] : []),
            ];
            $feeData = [
                'schedule_code' => $id,
                ...(isset($requestData['student_fee']) ? ['student_fee' => $requestData['student_fee'] ] : []),
                ...(isset($requestData['teacher_fee']) ? ['teacher_fee' => $requestData['teacher_fee'] ] : []),
            ];
            $clubSchedule = $this->clubScheduleRepository->getClubSchedule($id);
            if (!$clubSchedule) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubScheduleUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['club_schedule' => __('club_schedule.error.not_found')]
                );
            }
            $clubScheduleFee = $this->clubScheduleFeeRepository->getClubScheduleFee($id);
            if (!$clubScheduleFee) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubScheduleUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['club_schedule' => __('club_schedule.error.not_found_fee')]
                );
            }
            if ($request->user()->cannot('update', ClubSchedule::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubScheduleUpdate,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $clubSchedule->club->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubScheduleUpdate,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $clubSchedule = $this->clubScheduleRepository->update($clubSchedule->id, $scheduleData);

            $clubScheduleFee = $this->clubScheduleFeeRepository->update($clubScheduleFee->id, $feeData);

            DB::commit();
            return $this->sendResponse([...$clubSchedule->toArray(), ...$clubScheduleFee->toArray()], __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubScheduleUpdate);
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
            $clubSchedule = $this->clubScheduleRepository->getClubSchedule($id);
            if (!$clubSchedule) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubScheduleDelete,
                    Response::HTTP_NOT_FOUND,
                    ['club_schedule' => __('club_schedule.error.not_found')]
                );
            }
            if ($request->user()->cannot('destroy', ClubSchedule::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubScheduleDelete,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $clubSchedule->club->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubScheduleDelete,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $this->clubScheduleRepository->delete($clubSchedule->id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubScheduleDelete);
        }
    }
}
