<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\AbsenceReport\StoreAbsenceReportRequest;
use App\Http\Requests\AbsenceReport\UpdateAbsenceReportRequest;
use App\Http\Resources\AbsenceReportResource;
use App\Models\AbsenceReport;
use App\Repositories\AbsenceReportRepository;
use App\Repositories\ClubSessionRepository;
use Exception;
use HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AbsenceReportController extends Controller
{
    /**
     * @param AbsenceReportRepository $absenceReportRepository
     * @param ClubSessionRepository $clubSessionRepository
     */
    public function __construct(
        protected AbsenceReportRepository $absenceReportRepository,
        protected ClubSessionRepository   $clubSessionRepository
    )
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
            $club_session_id = $requestData['club_session_id'];
            $student_id = $requestData['student_id'];
            $club_session = $this->clubSessionRepository->find($club_session_id);

            $club_student_ids = $club_session->schedule->club->students->pluck('id')->toArray();
            if(!in_array($student_id, $club_student_ids)) {
                return $this->sendError(__('student.not_in_club'), ErrorCodeEnum::AbsenceReportStore);
            }

            $absence_report_student_ids = $club_session->absence_reports->pluck('student_id')->toArray();
            if(in_array($student_id, $absence_report_student_ids)) {
                return $this->sendError(__('absence_report.existed'), ErrorCodeEnum::AbsenceReportStore);
            }
            if ($request->user()->cannot('store', AbsenceReport::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club_session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
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
            if ($request->user()->cannot('update', AbsenceReport::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $absenceReport->session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
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
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $absenceReport = $this->absenceReportRepository->find($id);
            if (!$absenceReport) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::AbsenceReportDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', AbsenceReport::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $absenceReport->session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $this->absenceReportRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AbsenceReportDelete);
        }
    }

    public function getBySession(string $id): JsonResponse
    {
        $absenceReports = $this->absenceReportRepository->getAbsenceReportList(array(
            'session_id' => $id
        ));
        return $this->sendResponse($absenceReports);
    }
}
