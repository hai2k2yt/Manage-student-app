<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\ClubSession\StoreClubSessionRequest;
use App\Http\Requests\ClubSession\UpdateClubSessionRequest;
use App\Http\Resources\ClubSessionResource;
use App\Repositories\ClubSessionRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ClubSessionController extends Controller
{
    /**
     * @param ClubSessionRepository $clubSessionRepository
     */
    public function __construct(protected ClubSessionRepository $clubSessionRepository)
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
            $clubSession = $this->clubSessionRepository->create($requestData);
            $clubSessionResource = new ClubSessionResource($clubSession);
            DB::commit();
            return $this->sendResponse($clubSessionResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionStore);
        }
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
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $clubSession = $this->clubSessionRepository->find($id);
            if (!$clubSession) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubSessionDelete, Response::HTTP_NOT_FOUND);
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
