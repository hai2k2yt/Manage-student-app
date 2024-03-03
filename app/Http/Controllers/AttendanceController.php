<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Repositories\AttendanceRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{
    /**
     * @param AttendanceRepository $attendanceRepository
     */
    public function __construct(protected AttendanceRepository $attendanceRepository)
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

            $attendance = $this->attendanceRepository->update($id, $requestData);
            $attendanceResource = new AttendanceResource($attendance);
            DB::commit();
            return $this->sendResponse($attendanceResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceUpdate);
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
            $attendance = $this->attendanceRepository->find($id);
            if (!$attendance) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::AttendanceDelete, Response::HTTP_NOT_FOUND);
            }
            $this->attendanceRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AttendanceDelete);
        }
    }
}
