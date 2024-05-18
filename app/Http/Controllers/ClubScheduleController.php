<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubSchedule\StoreClubScheduleRequest;
use App\Http\Requests\ClubSchedule\UpdateClubScheduleRequest;
use App\Http\Resources\ClubScheduleResource;
use App\Models\ClubSchedule;
use App\Repositories\ClubRepository;
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
     */
    public function __construct(
        protected ClubScheduleRepository $clubScheduleRepository,
        protected ClubRepository         $clubRepository,
        protected TeacherRepository      $teacherRepository
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
            $club_code = $requestData['club_code'];
            $club = $this->clubRepository->getClub($club_code);
            if ($request->user()->cannot('store', ClubSchedule::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club->teacher_code != $requestTeacher->teacher_code)
                    throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $clubSchedule = $this->clubScheduleRepository->create($requestData);
            $clubScheduleResource = new ClubScheduleResource($clubSchedule);
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
        return $this->sendResponse($clubSchedule);
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
            $clubSchedule = $this->clubScheduleRepository->getClubSchedule($id);
            if (!$clubSchedule) {
                return $this->sendError(__('club_schedule.error.not_found'), ErrorCodeEnum::ClubScheduleUpdate, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('update', ClubSchedule::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $clubSchedule->club->teacher_code != $requestTeacher->teacher_code)
                    throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $clubSchedule = $this->clubScheduleRepository->update($clubSchedule->id, $requestData);
            $clubScheduleResource = new ClubScheduleResource($clubSchedule);
            DB::commit();
            return $this->sendResponse($clubScheduleResource, __('common.updated'));
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
                return $this->sendError(__('club_schedule.error.not_found'), ErrorCodeEnum::ClubScheduleDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', ClubSchedule::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $clubSchedule->club->teacher_code != $requestTeacher->teacher_code)
                    throw new HttpException(Response::HTTP_FORBIDDEN);
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
