<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Club\StoreClubRequest;
use App\Http\Requests\Club\UpdateClubRequest;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use App\Models\StudentClass;
use App\Repositories\ClubRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClubController extends Controller
{
    /**
     * @param ClubRepository $clubRepository
     */
    public function __construct(protected ClubRepository $clubRepository)
    {
    }

    public function all(): JsonResponse
    {
        $clubs = $this->clubRepository->getAll();
        $records = ClubResource::collection($clubs);
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
        $clubs = $this->clubRepository->getClubList($conditions);
        return $this->sendPaginationResponse($clubs, ClubResource::collection($clubs));
    }

    public function store(StoreClubRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->user()->cannot('store', Club::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $requestData = $request->validated();
            $club = $this->clubRepository->create($requestData);
            $clubResource = new ClubResource($club);
            DB::commit();
            return $this->sendResponse($clubResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubStore);
        }
    }

    public function show(string $id): JsonResponse
    {
        $club = $this->clubRepository->getClub($id);
        return $this->sendResponse($club);
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateClubRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClubRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club = $this->clubRepository->find($id);
            if (!$club) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubUpdate, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('update', $club)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $club = $this->clubRepository->update($id, $requestData);
            $clubResource = new ClubResource($club);
            DB::commit();
            return $this->sendResponse($clubResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubUpdate);
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
            $club = $this->clubRepository->find($id);
            if (!$club) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', Club::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $this->clubRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubDelete);
        }
    }
}
