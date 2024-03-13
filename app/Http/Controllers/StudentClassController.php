<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\StudentClass\AssignStudentToClassRequest;
use App\Http\Requests\StudentClass\StoreStudentClassRequest;
use App\Http\Requests\StudentClass\UpdateStudentClassRequest;
use App\Http\Resources\StudentClassResource;
use App\Repositories\StudentClassRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StudentClassController extends Controller
{
    /**
     * @param StudentClassRepository $studentClassRepository
     */
    public function __construct(protected StudentClassRepository $studentClassRepository)
    {
    }

    public function all(): JsonResponse
    {
        $studentClasses = $this->studentClassRepository->getAll();
        $records = StudentClassResource::collection($studentClasses);
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
        $studentClasses = $this->studentClassRepository->getStudentClassList($conditions);
        return $this->sendPaginationResponse($studentClasses, StudentClassResource::collection($studentClasses));
    }

    public function store(StoreStudentClassRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $studentClass = $this->studentClassRepository->create($requestData);
            $studentResource = new StudentClassResource($studentClass);
            DB::commit();
            return $this->sendResponse($studentResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentClassStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateStudentClassRequest $request
     * @return JsonResponse
     */
    public function update(string $id, UpdateStudentClassRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $student = $this->studentClassRepository->find($id);
            if (!$student) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::StudentUpdate, Response::HTTP_NOT_FOUND);
            }

            $student = $this->studentClassRepository->update($id, $requestData);
            $studentClassResource = new StudentClassResource($student);
            DB::commit();
            return $this->sendResponse($studentClassResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentClassUpdate);
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
            $studentClass = $this->studentClassRepository->find($id);
            if (!$studentClass) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::StudentClassDelete, Response::HTTP_NOT_FOUND);
            }
            $this->studentClassRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentClassDelete);
        }
    }

    public function assignStudents(AssignStudentToClassRequest $request) {

    }
}
