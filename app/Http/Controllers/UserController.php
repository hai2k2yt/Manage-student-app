<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\TeacherRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     */
    public function __construct(
        protected UserRepository    $userRepository,
        protected TeacherRepository $teacherRepository
    )
    {

    }

    public function all(Request $request): JsonResponse
    {
        $users = $this->userRepository->getAll();
        $records = UserResource::collection($users);
        return $this->sendResponse($records);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $conditions = $request->all();
        $users = $this->userRepository->getUserList($conditions);
        return $this->sendPaginationResponse($users, UserResource::collection($users));
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->sendError(
                null,
                ErrorCodeEnum::UserShow,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['user' => __('user.error.not_found')]
            );
        }
        return $this->sendResponse($user);
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->user()->cannot('update', User::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::UserUpdate,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            $requestData = $request->validated();
            $user = $this->userRepository->find($id);
            if (!$user) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::UserUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['user' => __('user.error.not_found')]
                );
            }

            $res = $this->userRepository->update($id, $requestData);
            if($user->role == RoleEnum::TEACHER->value) {
                $teacher = $this->teacherRepository->getTeacherByUserID($user->id);
                if($teacher) {
                    $teacher_name = $requestData['name'];
                    if($teacher_name) {
                        $this->teacherRepository->update($teacher->id, ['teacher_name' => $teacher_name]);
                    }
                }
            }
            $userResource = new UserResource($res);
            DB::commit();
            return $this->sendResponse($userResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::StudentUpdate);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::UserDelete,
                    Response::HTTP_NOT_FOUND,
                    ['user' => __('user.error.not_found')]
                );
            }
            if ($request->user()->cannot('destroy', $user)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::UserDelete,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($user->role == RoleEnum::TEACHER->value) {
                $teacher = $this->teacherRepository->getTeacherByUserID($user->id);
                if($teacher) {
                    $this->teacherRepository->delete($teacher->id);
                }
            }
            $this->userRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::AbsenceReportDelete);
        }
    }
}
