<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $customerCorporations = $this->customerRepository->getCustomerCorporations($request->all());

        return $this->sendPaginationResponse($customerCorporations, UserCorporationResource::collection($customerCorporations));
    }
}
