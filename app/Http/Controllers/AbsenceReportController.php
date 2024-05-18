<?php

namespace App\Http\Controllers;

use App\Enums\AbsenceReportEnum;
use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\AbsenceReport\StoreAbsenceReportRequest;
use App\Http\Requests\AbsenceReport\UpdateAbsenceReportRequest;
use App\Http\Resources\AbsenceReportResource;
use App\Models\AbsenceReport;
use App\Models\Teacher;
use App\Repositories\AbsenceReportRepository;
use App\Repositories\ClubSessionRepository;
use App\Repositories\TeacherRepository;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AbsenceReportController extends Controller
{
    /**
     * @param AbsenceReportRepository $absenceReportRepository
     * @param ClubSessionRepository $clubSessionRepository
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(
        protected AbsenceReportRepository $absenceReportRepository,
        protected ClubSessionRepository   $clubSessionRepository,
        protected TeacherRepository       $teacherRepository,
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
            $session_code = $requestData['session_code'];
            $student_code = $requestData['student_code'];
            $club_session = $this->clubSessionRepository->getClubSession($session_code);

            $club_student_codes = $club_session->schedule->club->students->pluck('student_code')->toArray();
            if (!in_array($student_code, $club_student_codes)) {
                return $this->sendError(__('student.error.not_in_club'), ErrorCodeEnum::AbsenceReportStore);
            }

            $absence_report_student_codes = $club_session->absence_reports->pluck('student_code')->toArray();
            if (in_array($student_code, $absence_report_student_codes)) {
                return $this->sendError(__('absence_report.existed'), ErrorCodeEnum::AbsenceReportStore);
            }
            if ($request->user()->cannot('store', AbsenceReport::class)) {
                return $this->sendError(__('auth.forbidden'), ErrorCodeEnum::AbsenceReportStore, Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club_session->schedule->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(__('auth.forbidden'), ErrorCodeEnum::AbsenceReportStore, Response::HTTP_FORBIDDEN);
            }
            $absenceReport = $this->absenceReportRepository->create([...$requestData, 'status' => AbsenceReportEnum::PENDING->value]);
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
                return $this->sendError(__('absence_report.error.not_found'), ErrorCodeEnum::AbsenceReportUpdate, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('update', $absenceReport)) {
                return $this->sendError(__('auth.forbidden'), ErrorCodeEnum::AbsenceReportUpdate, Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $absenceReport->session->schedule->teacher_code != $requestTeacher->teacher_code) {
                    return $this->sendError(__('auth.forbidden'), ErrorCodeEnum::AbsenceReportUpdate, Response::HTTP_FORBIDDEN);
                }
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
                return $this->sendError(__('absence_report.error.not_found'), ErrorCodeEnum::AbsenceReportDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', $absenceReport)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $absenceReport->session->schedule->teacher_code != $requestTeacher->teacher_code) {
                    throw new HttpException(Response::HTTP_FORBIDDEN);
                }
            }
            $this->absenceReportRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AbsenceReportDelete);
        }
    }

    public function getBySession(string $id): JsonResponse
    {
        $absenceReports = $this->absenceReportRepository->getAbsenceReportList(array(
            'session_code' => $id
        ));
        return $this->sendResponse($absenceReports);
    }

    public function getClubStudent(Request $request)
    {
        $res = $request->all();
        $student_code = $res['student_code'];
        $club_code = $res['club_code'];

        $absence_reports = $this->absenceReportRepository->byClubStudent($student_code, $club_code);
        return $this->sendResponse($absence_reports);
    }
}
