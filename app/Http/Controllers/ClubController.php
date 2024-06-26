<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Club\StoreClubRequest;
use App\Http\Requests\Club\UpdateClubRequest;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use App\Models\ClubSchedule;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Repositories\ClubRepository;
use App\Repositories\ClubScheduleRepository;
use App\Repositories\TeacherRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClubController extends Controller
{
    /**
     * @param ClubRepository $clubRepository
     * @param TeacherRepository $teacherRepository
     * @param ClubScheduleRepository $clubScheduleRepository
     */
    public function __construct(
        protected ClubRepository         $clubRepository,
        protected TeacherRepository      $teacherRepository,
        protected ClubScheduleRepository $clubScheduleRepository
    )
    {
    }

    public function all(): JsonResponse
    {
        $clubs = $this->clubRepository->getAll();
        $records = ClubResource::collection($clubs);
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
        $clubs = $this->clubRepository->getClubList($conditions);
        return $this->sendPaginationResponse($clubs, ClubResource::collection($clubs));
    }

    public function store(StoreClubRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if ($request->user()->cannot('store', Club::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubStore,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            $requestData = $request->validated();
            $club = $this->clubRepository->create($requestData);
            $clubResource = new ClubResource($club);
            DB::commit();
            return $this->sendResponse($clubResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubStore);
        }
    }

    public function show(string $id): JsonResponse
    {
        $club = $this->clubRepository->getClub($id);
        if (!$club) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubShow,
                Response::HTTP_NOT_FOUND,
                ['club' => __('club.error.not_found')]
            );
        }
        return $this->sendResponse($club, __('common.get_success'));
    }

    public function me(Request $request)
    {
        $params = $request->all();
        if ($request->user()->cannot('me', Club::class)) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubMe,
                Response::HTTP_FORBIDDEN,
                ['auth' => __('auth.forbidden')]
            );
        }
        $id = $request->user()->id;
        $teacher = $this->teacherRepository->getTeacherByUserID($id);
        $teacher_code = $teacher->teacher_code;
        $club_codes = ClubSchedule::where('teacher_code', $teacher_code)->pluck('club_code')->all();
        $clubs = $this->clubRepository->getByConditions([...$params, 'club_code' => $club_codes], ['*'], ['teacher']);
        return $this->sendPaginationResponse($clubs, ClubResource::collection($clubs));
    }

    public function students(string $id): JsonResponse
    {
        $club = $this->clubRepository->getClub($id);
        if (!$club) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubGetStudents,
                Response::HTTP_NOT_FOUND,
                ['club' => __('club.error.not_found')]
            );
        }
        return $this->sendResponse($club->students, __('common.get_success'));
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateClubRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClubRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club = $this->clubRepository->getClub($id);
            if (!$club) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['club' => __('club.error.not_found')]
                );
            }
            if ($request->user()->cannot('update', $club)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubUpdate,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            $clubData = $this->clubRepository->update($club->id, $requestData);
            $clubResource = new ClubResource($clubData);
            DB::commit();
            return $this->sendResponse($clubResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubUpdate);
        }
    }

    /**
     * Delete corporation department.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $club = $this->clubRepository->getClub($id);
            if (!$club) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubDelete,
                    Response::HTTP_NOT_FOUND,
                    ['club' => __('club.error.not_found')]
                );
            }
            if ($request->user()->cannot('destroy', Club::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubDelete,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            $this->clubRepository->delete($club->id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubDelete);
        }
    }
}
