<?php

namespace App\Http\Controllers;

use App\Enums\ClubEnrollmentStatusEnum;
use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubEnrollment\AssignStudentToClubRequest;
use App\Http\Requests\ClubEnrollment\CancelClubEnrollmentRequest;
use App\Http\Requests\ClubEnrollment\StoreClubEnrollmentRequest;
use App\Http\Requests\ClubEnrollment\UpdateClubEnrollmentRequest;
use App\Http\Resources\ClubEnrollmentResource;
use App\Models\Club;
use App\Models\ClubEnrollment;
use App\Models\ClubEnrollmentHistory;
use App\Repositories\ClubEnrollmentRepository;
use App\Repositories\ClubRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClubEnrollmentController extends Controller
{
    /**
     * @param ClubEnrollmentRepository $clubEnrollmentRepository
     * @param ClubRepository $clubRepository
     * @param StudentRepository $studentRepository
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(
        protected ClubEnrollmentRepository $clubEnrollmentRepository,
        protected ClubRepository           $clubRepository,
        protected StudentRepository        $studentRepository,
        protected TeacherRepository        $teacherRepository
    )
    {
    }

    public function all(): JsonResponse
    {
        $clubEnrollments = $this->clubEnrollmentRepository->getAll();
        $records = ClubEnrollmentResource::collection($clubEnrollments);
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
        $clubEnrollments = $this->clubEnrollmentRepository->getClubEnrollmentList($conditions);
        return $this->sendPaginationResponse($clubEnrollments, ClubEnrollmentResource::collection($clubEnrollments));
    }

    public function store(StoreClubEnrollmentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club_code = $requestData['club_code'];
            $student_code = $requestData['student_code'];
            $from = $requestData['from'];

            $club = $this->clubRepository->getClub($club_code);

            if ($request->user()->cannot('store', ClubEnrollment::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentStore,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubEnrollmentStore,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }

            $currentEnrollments =
                ClubEnrollment::where('club_code', $club_code)
                    ->where('student_code', $student_code)->first();

            // Not registered before
            if (!$currentEnrollments) {
                $clubEnrollment = ClubEnrollment::create([
                    'club_code' => $club_code,
                    'student_code' => $student_code,
                    'status' => ClubEnrollmentStatusEnum::STUDY->value
                ]);
                $createdEnrollment = ClubEnrollment::where('club_code', $club_code)
                    ->where('student_code', $student_code)
                    ->where('status', ClubEnrollmentStatusEnum::STUDY->value)
                    ->first();
                $enrollment_history = ClubEnrollmentHistory::create([
                    'club_enrollment_id' => $createdEnrollment->id,
                    'from' => $from,
                    'status' => ClubEnrollmentStatusEnum::STUDY->value
                ]);

                DB::commit();
                return $this->sendResponse(null, __('common.created'), Response::HTTP_CREATED);
            }

            // Registered before
            // Studying
            if ($currentEnrollments->status == ClubEnrollmentStatusEnum::STUDY->value) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentStore,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['club_enrollment' => __('club_enrollment.error.existed')]
                );
            }
            $currentEnrollments->update([
                'status' => ClubEnrollmentStatusEnum::STUDY->value
            ]);
            //Absence
            $enrollment_history =
                ClubEnrollmentHistory::where('club_enrollment_id', $currentEnrollments->id)
                    ->where('from', '<=', $from)
                    ->where('to', '>=', $from)
                    ->get();
            if ($enrollment_history->count() > 0) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentStore,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['club_enrollment' => __('club_enrollment.error.date_not_valid')]
                );
            }
            ClubEnrollmentHistory::create([
                'club_enrollment_id' => $currentEnrollments->id,
                'from' => $from,
                'status' => ClubEnrollmentStatusEnum::STUDY->value
            ]);
            DB::commit();
            return $this->sendResponse(null, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentStore);
        }
    }

    public function cancelEnrollment(CancelClubEnrollmentRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $to = $requestData['to'] ?? date('Y-m-d');
            $club_enrollment_id = $id;
            $club_enrollment = $this->clubEnrollmentRepository->find($club_enrollment_id);
            if (!$club_enrollment) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentCancel,
                    Response::HTTP_NOT_FOUND,
                    ['club_enrollment' => __('club_enrollment.error.not_found')]
                );
            }
            if ($request->user()->cannot('cancel', $club_enrollment)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentCancel,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($club_enrollment->status == ClubEnrollmentStatusEnum::ABSENCE) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentCancel,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['club_enrollment' => __('club_enrollment.error.absence')]
                );
            }
            $enrollment_histories_check = ClubEnrollmentHistory
                ::where('club_enrollment_id', $club_enrollment_id)
                ->where(static function ($query) use ($to) {
                    $query->where(static function ($query2) use ($to) {
                        $query2
                            ->where('from', '<=', $to)
                            ->where('to', '>=', $to)
                            ->where('status', ClubEnrollmentStatusEnum::ABSENCE);
                    })->orWhere(static function ($query3) use ($to) {
                        $query3
                            ->where('from', '>=', $to)
                            ->where('status', ClubEnrollmentStatusEnum::STUDY);
                    });
                })->count();
            if ($enrollment_histories_check) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentCancel,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['club_enrollment' => __('club_enrollment.error.to_not_valid')]
                );
            }

            $this->clubEnrollmentRepository->update($club_enrollment_id, ['status' => ClubEnrollmentStatusEnum::ABSENCE]);

            $current_enrollment_history = $club_enrollment->enrollment_histories()->where('status', ClubEnrollmentStatusEnum::STUDY);
            $current_enrollment_history->update(['status' => ClubEnrollmentStatusEnum::ABSENCE, 'to' => $to]);
            DB::commit();
            return $this->sendResponse(null, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentCancel);
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
            $clubEnrollment = $this->clubEnrollmentRepository->find($id);
            if (!$clubEnrollment) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentDelete,
                    Response::HTTP_NOT_FOUND,
                    ['club_enrollment' => __('club_enrollment.error.not_found')]
                );
            }
            $club_code = $clubEnrollment->club_code;
            $club = $this->clubRepository->getClub($club_code);
            if (!$club) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentDelete,
                    Response::HTTP_NOT_FOUND,
                    ['club' => __('club.error.not_found')]
                );
            }
            if ($request->user()->cannot('destroy', ClubEnrollment::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentDelete,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubEnrollmentDelete,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $this->clubEnrollmentRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentDelete);
        }
    }

    public function assignStudents(AssignStudentToClubRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club_code = $requestData['club_code'];
            $club = $this->clubRepository->getClub($club_code);
            if ($request->user()->cannot('assignStudents', ClubEnrollment::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubEnrollmentAssignStudent,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher || $club->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubEnrollmentAssignStudent,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $student_codes = $requestData['student_codes'];
            $response = [
                'success' => [],
                'not_found' => [],
                'existed' => []
            ];

            foreach ($student_codes as $student_code) {
                $student = $this->studentRepository->getStudent($student_code);
                if (!$student) {
                    $response['not_found'][] = $student_code;
                    break;
                }
                $registered = $this->clubEnrollmentRepository->getByConditions(array('student_code' => $student_code, 'club_code' => $club_code));
                if ($registered) {
                    $this->clubEnrollmentRepository->create(array('student_code' => $student_code, 'club_code' => $club_code));
                    $response['success'][] = $student_code;
                } else {
                    $response['existed'][] = $student_code;
                }
            }
            DB::commit();
            return $this->sendResponse($response, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentAssignStudent);
        }
    }
}
