<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * @param StudentRepository $studentRepository
     */
    public function __construct(protected StudentRepository $studentRepository)
    {
    }

    public function all(): JsonResponse
    {
        $students = $this->studentRepository->getAll();
        $records = StudentResource::collection($students);
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
        $students = $this->studentRepository->getStudentList($conditions);
        return $this->sendPaginationResponse($students, StudentResource::collection($students));
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $student = $this->studentRepository->create($requestData);
            $studentResource = new StudentResource($student);
            DB::commit();
            return $this->sendResponse($studentResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateStudentRequest $request
     * @return JsonResponse
     */
    public function update( string $id, Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->all();
            dd($requestData);
            $student = $this->studentRepository->find($id);
            if (!$student) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::StudentUpdate, Response::HTTP_NOT_FOUND);
            }

            $student = $this->studentRepository->update($id, $requestData);
            $studentResource = new StudentResource($student);
            DB::commit();
            return $this->sendResponse($studentResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentUpdate);
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
            $student = $this->studentRepository->find($id);
            if (!$student) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::StudentDelete, Response::HTTP_NOT_FOUND);
            }
            $this->studentRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentDelete);
        }
    }
}
