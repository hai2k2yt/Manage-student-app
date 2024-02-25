<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(protected UserRepository $userRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->getAll();

        return $this->sendPaginationResponse($users, UserResource::collection($users));
    }
}
