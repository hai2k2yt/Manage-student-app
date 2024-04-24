<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeacherResource;
use App\Repositories\TeacherRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeacherController extends Controller
{
    public function __construct(protected TeacherRepository $teacherRepository)
    {
    }

    public function all(Request $request): JsonResponse
    {
        $teachers = $this->teacherRepository->getAll();
        $records = TeacherResource::collection($teachers);
        return $this->sendResponse($records);
    }
}
