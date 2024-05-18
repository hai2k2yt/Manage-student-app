<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Resources\TeacherResource;
use App\Repositories\TeacherRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function showByUserId(Request $request, string $user_id) {
        $teacher = $this->teacherRepository->getTeacherByUserID($user_id);
        if(!$teacher) {
            return $this->sendError(
                null,
                ErrorCodeEnum::TeacherShow,
                Response::HTTP_NOT_FOUND,
                ['teacher' => __('teacher.error.not_found')]
            );
        }
        return $this->sendResponse($teacher);

    }
}
