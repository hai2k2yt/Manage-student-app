<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\ClubEnrollment\AssignStudentToClubRequest;
use App\Http\Requests\ClubEnrollment\StoreClubEnrollmentRequest;
use App\Http\Requests\ClubEnrollment\UpdateClubEnrollmentRequest;
use App\Http\Resources\ClubEnrollmentResource;
use App\Repositories\ClubEnrollmentRepository;
use App\Repositories\ClubRepository;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ClubEnrollmentController extends Controller
{
    /**
     * @param ClubEnrollmentRepository $clubEnrollmentRepository
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
            $club = $this->clubEnrollmentRepository->create($requestData);
            $clubResource = new ClubEnrollmentResource($club);
            DB::commit();
            return $this->sendResponse($clubResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateClubEnrollmentRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClubEnrollmentRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club = $this->clubEnrollmentRepository->find($id);
            if (!$club) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubEnrollmentUpdate, Response::HTTP_NOT_FOUND);
            }

            $club = $this->clubEnrollmentRepository->update($id, $requestData);
            $clubEnrollmentResource = new ClubEnrollmentResource($club);
            DB::commit();
            return $this->sendResponse($clubEnrollmentResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubUpdate);
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
            $clubEnrollment = $this->clubEnrollmentRepository->find($id);
            if (!$clubEnrollment) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubEnrollmentDelete, Response::HTTP_NOT_FOUND);
            }
            $this->clubEnrollmentRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubEnrollmentDelete);
        }
    }

    public function assignStudents(AssignStudentToClubRequest $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club_id = $requestData->club_id;
            $club = $this->clubRepository->find($club_id);
            if (!$club) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubEnrollmentAssignStudent, Response::HTTP_NOT_FOUND);
            }
            $student_ids = $requestData->student_ids;
            $response = [
                'success' => [],
                'not_found' => [],
                'existed' => []
            ];

            foreach ($student_ids as $id) {
                $student = $this->studentRepository->find($id);
                if(!$student) {
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
