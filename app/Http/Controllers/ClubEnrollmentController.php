<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubEnrollment\AssignStudentToClubRequest;
use App\Http\Requests\ClubEnrollment\StoreClubEnrollmentRequest;
use App\Http\Requests\ClubEnrollment\UpdateClubEnrollmentRequest;
use App\Http\Resources\ClubEnrollmentResource;
use App\Models\Club;
use App\Models\ClubEnrollment;
use App\Repositories\ClubEnrollmentRepository;
use App\Repositories\ClubRepository;
use App\Repositories\StudentRepository;
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
     */
    public function __construct(
        protected ClubEnrollmentRepository $clubEnrollmentRepository,
        protected ClubRepository           $clubRepository,
        protected StudentRepository        $studentRepository
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
            $club_id = $requestData['club_id'];
            $club = $this->clubRepository->find($club_id);

            if (ClubEnrollment::where('club_id', $club_id)->where('teacher_id', $request->user()->id)) {
                return $this->sendError(__('common.existed'), ErrorCodeEnum::ClubEnrollmentStore);
            }
            if ($request->user()->cannot('store', ClubEnrollment::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $clubEnrollment = $this->clubEnrollmentRepository->create($requestData);
            $clubEnrollmentResource = new ClubEnrollmentResource($clubEnrollment);
            DB::commit();
            return $this->sendResponse($clubEnrollmentResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentStore);
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
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubEnrollmentDelete, Response::HTTP_NOT_FOUND);
            }
            $club_id = $clubEnrollment->club_id;
            $club = $this->clubRepository->find($club_id);
            if (!$club) {
                return $this->sendError(__('club.not_found'), ErrorCodeEnum::ClubEnrollmentDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', ClubEnrollment::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $this->clubEnrollmentRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
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
            $club_id = $requestData['club_id'];
            $club = $this->clubRepository->find($club_id);
            if ($request->user()->cannot('assignStudents', ClubEnrollment::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $student_ids = $requestData->student_ids;
            $response = [
                'success' => [],
                'not_found' => [],
                'existed' => []
            ];

            foreach ($student_ids as $id) {
                $student = $this->studentRepository->find($id);
                if (!$student) {
                    $response['not_found'][] = $id;
                    break;
                }
                $registered = $this->clubEnrollmentRepository->getByConditions(array('student_id' => $id, 'club_id' => $club_id));
                if ($registered) {
                    $this->clubEnrollmentRepository->create(array('student_id' => $id, 'club_id' => $club_id));
                    $response['success'][] = $id;
                } else {
                    $response['existed'][] = $id;
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
