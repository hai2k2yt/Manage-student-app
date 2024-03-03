<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\AbsenceReport\StoreAbsenceReportRequest;
use App\Http\Requests\AbsenceReport\UpdateAbsenceReportRequest;
use App\Http\Resources\AbsenceReportResource;
use App\Repositories\AbsenceReportRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AbsenceReportController extends Controller
{
    /**
     * @param AbsenceReportRepository $absenceReportRepository
     */
    public function __construct(protected AbsenceReportRepository $absenceReportRepository)
    {
    }

    public function all(): JsonResponse
    {
        $absenceReports = $this->absenceReportRepository->getAll();
        $records = AbsenceReportResource::collection($absenceReports);
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
        $absenceReports = $this->absenceReportRepository->getAbsenceReportList($conditions);
        return $this->sendPaginationResponse($absenceReports, AbsenceReportResource::collection($absenceReports));
    }

    public function store(StoreAbsenceReportRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $absenceReport = $this->absenceReportRepository->create($requestData);
            $absenceReportResource = new AbsenceReportResource($absenceReport);
            DB::commit();
            return $this->sendResponse($absenceReportResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AbsenceReportStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateAbsenceReportRequest $request
     * @return JsonResponse
     */
    public function update(UpdateAbsenceReportRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $absenceReport = $this->absenceReportRepository->find($id);
            if (!$absenceReport) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::AbsenceReportUpdate, Response::HTTP_NOT_FOUND);
            }

            $absenceReport = $this->absenceReportRepository->update($id, $requestData);
            $absenceReportResource = new AbsenceReportResource($absenceReport);
            DB::commit();
            return $this->sendResponse($absenceReportResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AbsenceReportUpdate);
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
            $absenceReport = $this->absenceReportRepository->find($id);
            if (!$absenceReport) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::AbsenceReportDelete, Response::HTTP_NOT_FOUND);
            }
            $this->absenceReportRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AbsenceReportDelete);
        }
    }
}
