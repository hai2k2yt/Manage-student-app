<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentController extends Controller
{
    /**
     * @param StudentRepository $studentRepository
     */
    public function __construct(protected StudentRepository $studentRepository)
    {
    }

    public function all(Request $request): JsonResponse
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
            if($request->user()->cannot('store', Student::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
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

    public function show(string $id): JsonResponse
    {
        $student = $this->studentRepository->getStudent($id);
        return $this->sendResponse($student);
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateStudentRequest $request
     * @return JsonResponse
     */
    public function update(UpdateStudentRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            if($request->user()->cannot('update', Student::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $requestData = $request->validated();
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
    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            if($request->user()->cannot('destroy', Student::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
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
