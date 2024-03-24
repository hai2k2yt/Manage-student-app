<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\StudentClass\AssignStudentToClassRequest;
use App\Http\Requests\StudentClass\StoreStudentClassRequest;
use App\Http\Requests\StudentClass\UpdateStudentClassRequest;
use App\Http\Resources\StudentClassResource;
use App\Models\Student;
use App\Models\StudentClass;
use App\Repositories\StudentClassRepository;
use App\Repositories\StudentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StudentClassController extends Controller
{
    /**
     * @param StudentClassRepository $studentClassRepository
     * @param StudentRepository $studentRepository
     *
     */
    public function __construct(
        protected StudentClassRepository $studentClassRepository,
        protected StudentRepository      $studentRepository
    )
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
            if ($request->user()->cannot('store', StudentClass::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $requestData = $request->validated();
            $studentClass = $this->studentClassRepository->create($requestData);
            $studentResource = new StudentClassResource($studentClass);
            DB::commit();
            return $this->sendResponse($studentResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClassStore);
        }
    }

    public function show(string $id): JsonResponse
    {
        $studentClass = $this->studentClassRepository->getStudentClass($id);
        return $this->sendResponse($studentClass);
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
            if ($request->user()->cannot('update', StudentClass::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
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
            return $this->sendExceptionError($error, ErrorCodeEnum::ClassUpdate);
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
            if ($request->user()->cannot('destroy', StudentClass::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $studentClass = $this->studentClassRepository->find($id);
            if (!$studentClass) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClassDelete, Response::HTTP_NOT_FOUND);
            }
            $this->studentClassRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClassDelete);
        }
    }

    public function assignStudents(AssignStudentToClassRequest $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $student_ids = $requestData->student_ids;
            $class_id = $requestData->class_id;
            $class = $this->studentClassRepository->find($class_id);
            if (!$class) {
                return $this->sendResponse('common.not_found', __('common.updated'));
            }
            if ($request->user()->cannot('assignStudents', $class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $response = [
                "update_success" => [],
                "not_found" => [],
            ];
            foreach ($student_ids as $id) {
                $student = $this->studentRepository->find($id);
                if (!$student) {
                    $response["not_found"][] = $id;
                    break;
                }
                $this->studentRepository->update($id,
                    array('class_id' => $class_id));
                $response["update_success"][] = $id;
            }
            DB::commit();
            return $this->sendResponse($response, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClassAssignStudent);
        }
    }
}
