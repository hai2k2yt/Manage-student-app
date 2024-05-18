<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     */
    public function __construct(
        protected UserRepository $userRepository
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
        $user = User::find($id);
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
}
