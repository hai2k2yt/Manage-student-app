<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubSessionPhoto\StoreClubSessionPhotoRequest;
use App\Http\Requests\ClubSessionPhoto\UpdateClubSessionPhotoRequest;
use App\Http\Resources\ClubSessionPhotoResource;
use App\Models\ClubSessionPhoto;
use App\Repositories\ClubRepository;
use App\Repositories\ClubSessionPhotoRepository;
use App\Repositories\ClubSessionRepository;
use App\Repositories\TeacherRepository;
use Exception;
use HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ClubSessionPhotoController extends Controller
{
    /**
     * @param ClubSessionPhotoRepository $clubSessionPhotoRepository
     * @param ClubSessionRepository $clubSessionRepository
     * @param ClubRepository $clubRepository
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(
        protected ClubSessionPhotoRepository $clubSessionPhotoRepository,
        protected ClubSessionRepository      $clubSessionRepository,
        protected ClubRepository             $clubRepository,
        protected TeacherRepository          $teacherRepository
    )
    {
    }

    public function all(): JsonResponse
    {
        $clubSessionPhotos = $this->clubSessionPhotoRepository->getAll();
        $records = ClubSessionPhotoResource::collection($clubSessionPhotos);
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
        $clubSessionPhotos = $this->clubSessionPhotoRepository->getClubSessionPhotoList($conditions);
        return $this->sendPaginationResponse($clubSessionPhotos, ClubSessionPhotoResource::collection($clubSessionPhotos));
    }

    public function store(StoreClubSessionPhotoRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $session_code = $requestData['session_code'];
            $club_session = $this->clubSessionRepository->find($session_code);
            if ($request->user()->cannot('store', ClubSessionPhoto::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubSessionPhotoStore,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher && $club_session->schedule->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubSessionPhotoStore,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $path = Storage::putFile('session-photo', $requestData['photo_url'], 'public');

            $clubSessionPhoto = $this->clubSessionPhotoRepository->create([
                "session_code" => $session_code,
                "photo_url" => 'storage/' . $path
            ]);
            $clubSessionPhotoResource = new ClubSessionPhotoResource($clubSessionPhoto);
            DB::commit();
            return $this->sendResponse($clubSessionPhotoResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionPhotoStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateClubSessionPhotoRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClubSessionPhotoRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $club_session_photo = $this->clubSessionPhotoRepository->find($id);
            if (!$club_session_photo) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubSessionPhotoUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['club_session_photo' => __('club_session_photo.error.not_found')]
                );
            }
            if ($request->user()->cannot('update', ClubSessionPhoto::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubSessionPhotoUpdate,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher && $club_session_photo->session->schedule->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubSessionPhotoUpdate,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $path = Storage::putFile('public', $requestData['photo_url']);
            $requestData['photo_url'] = $path;
            $clubSessionPhoto = $this->clubSessionPhotoRepository->find($id);
            if (!$clubSessionPhoto) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubSessionPhotoUpdate,
                    Response::HTTP_NOT_FOUND,
                    ['club_session_photo' => __('club_session_photo.error.not_found')]
                );
            }
            $clubSessionPhoto = $this->clubSessionPhotoRepository->update($id, $requestData);
            $clubSessionPhotoResource = new ClubSessionPhotoResource($clubSessionPhoto);
            DB::commit();
            return $this->sendResponse($clubSessionPhotoResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionPhotoUpdate);
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
            $club_session_photo = $this->clubSessionPhotoRepository->find($id);
            if (!$club_session_photo) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubSessionPhotoDelete,
                    Response::HTTP_NOT_FOUND,
                    ['club_session_photo' => __('club_session_photo.error.not_found')]
                );
            }
            if ($request->user()->cannot('destroy', ClubSessionPhoto::class)) {
                return $this->sendError(
                    null,
                    ErrorCodeEnum::ClubSessionPhotoDelete,
                    Response::HTTP_FORBIDDEN,
                    ['auth' => __('auth.forbidden')]
                );
            }
            if ($request->user()->role == RoleEnum::TEACHER->value) {
                $requestTeacher = $this->teacherRepository->getTeacherByUserID($request->user()->id);
                if (!$requestTeacher && $club_session_photo->session->schedule->teacher_code != $requestTeacher->teacher_code)
                    return $this->sendError(
                        null,
                        ErrorCodeEnum::ClubSessionPhotoDelete,
                        Response::HTTP_FORBIDDEN,
                        ['auth' => __('auth.forbidden')]
                    );
            }
            $this->clubSessionPhotoRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'));
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionPhotoDelete);
        }
    }

    public function getByClub(string $id): JsonResponse
    {
        $club = $this->clubRepository->getClub($id);
        if (!$club) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubSessionPhotoByClub,
                Response::HTTP_NOT_FOUND,
                ['club' => __('club.error.not_found')]
            );
        }
        $session_codes = [];
        $club->schedules->each(function ($schedule) use (&$session_codes) {
            $schedule->sessions->each(function ($session) use (&$session_codes) {
                $session_codes[] = $session->id;
            });
        });

        $club_session_photos = $this->clubSessionPhotoRepository->getClubSessionPhotoList(
            ['session_code' => $session_codes]
        );
        return $this->sendResponse($club_session_photos, __('common.get_success'));

    }

    public function getByClubSession(string $id): JsonResponse
    {
        $session = $this->clubSessionRepository->getClubSession($id);
        if (!$session) {
            return $this->sendError(
                null,
                ErrorCodeEnum::ClubSessionPhotoBySession,
                Response::HTTP_NOT_FOUND,
                ['club_session' => __('club_session.error.not_found')]
            );
        }
        $club_session_photos = $this->clubSessionPhotoRepository->getClubSessionPhotoList(
            ['session_code' => $id]
        );
        return $this->sendResponse($club_session_photos, __('common.get_success'));
    }
}
