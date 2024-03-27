<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     */
    public function __construct()
    {
    }

    public function all(Request $request): JsonResponse
    {
        $users = User::all();
        $records = UserResource::collection($users);
        return $this->sendResponse($records);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->getAll();

        return $this->sendPaginationResponse($users, UserResource::collection($users));
    }

    public function show(string $id): JsonResponse
    {
        $user = User::find($id);
        if(!$user) {
            return $this->sendError(__('user.not_found'));
        }
        return $this->sendResponse($user);
    }
}
