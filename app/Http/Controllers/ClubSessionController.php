<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubSession\StoreClubSessionRequest;
use App\Http\Requests\ClubSession\UpdateClubSessionRequest;
use App\Http\Resources\ClubSessionResource;
use App\Models\ClubSession;
use App\Repositories\ClubScheduleRepository;
use App\Repositories\ClubSessionRepository;
use Exception;
use HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ClubSessionController extends Controller
{
    /**
     * @param ClubSessionRepository $clubSessionRepository
     * @param ClubScheduleRepository $clubScheduleRepository
     */
    public function __construct(
        protected ClubSessionRepository  $clubSessionRepository,
        protected ClubScheduleRepository $clubScheduleRepository
    )
    {
    }

    public function all(): JsonResponse
    {
        $clubSessions = $this->clubSessionRepository->getAll();
        $records = ClubSessionResource::collection($clubSessions);
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
        $clubSessions = $this->clubSessionRepository->getClubSessionList($conditions);
        return $this->sendPaginationResponse($clubSessions, ClubSessionResource::collection($clubSessions));
    }

    public function store(StoreClubSessionRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $schedule_id = $requestData['schedule_id'];
            $clubSchedule = $this->clubScheduleRepository->find($schedule_id);
            if ($request->user()->cannot('store', ClubSession::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $clubSchedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $clubSession = $this->clubSessionRepository->create($requestData);
            $clubSessionResource = new ClubSessionResource($clubSession);
            DB::commit();
            return $this->sendResponse($clubSessionResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionStore);
        }
    }

    public function show(string $id): JsonResponse
    {
        $clubSession = $this->clubSessionRepository->getClubSession($id);
        return $this->sendResponse($clubSession);
    }

    public function getByClub(string $id): JsonResponse
    {
        $clubSessions = $this->clubSessionRepository->getByClubId($id);

        return $this->sendResponse($clubSessions);
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateClubSessionRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClubSessionRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $clubSession = $this->clubSessionRepository->find($id);
            if (!$clubSession) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubSessionUpdate, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('update', ClubSession::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $clubSession->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $clubSession = $this->clubSessionRepository->update($id, $requestData);
            $clubSessionResource = new ClubSessionResource($clubSession);
            DB::commit();
            return $this->sendResponse($clubSessionResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionUpdate);
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
            $clubSession = $this->clubSessionRepository->find($id);
            if (!$clubSession) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubSessionDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', ClubSession::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $clubSession->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $this->clubSessionRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionDelete);
        }
    }
}
