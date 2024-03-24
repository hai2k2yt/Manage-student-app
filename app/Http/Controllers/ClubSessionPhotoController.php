<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\ClubSessionPhoto\StoreClubSessionPhotoRequest;
use App\Http\Requests\ClubSessionPhoto\UpdateClubSessionPhotoRequest;
use App\Http\Resources\ClubSessionPhotoResource;
use App\Models\ClubSessionPhoto;
use App\Repositories\ClubSessionPhotoRepository;
use App\Repositories\ClubSessionRepository;
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
     */
    public function __construct(
        protected ClubSessionPhotoRepository $clubSessionPhotoRepository,
        protected ClubSessionRepository      $clubSessionRepository
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
            $session_id = $requestData['session_id'];
            $club_session = $this->clubSessionRepository->find($session_id);
            if ($request->user()->cannot('store', ClubSessionPhoto::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club_session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $path = Storage::putFile('public', $requestData['photo_url']);

            $clubSessionPhoto = $this->clubSessionPhotoRepository->create([
                "session_id" => $session_id,
                "photo_url" => $path
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
            if(!$club_session_photo) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubSessionPhotoUpdate, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('update', ClubSessionPhoto::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $club_session_photo->session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $path = Storage::putFile('public', $requestData['photo_url']);
            $requestData['photo_url'] = $path;
            $clubSessionPhoto = $this->clubSessionPhotoRepository->find($id);
            if (!$clubSessionPhoto) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubSessionPhotoUpdate, Response::HTTP_NOT_FOUND);
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
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $clubSessionPhoto = $this->clubSessionPhotoRepository->find($id);
            if (!$clubSessionPhoto) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::ClubSessionPhotoDelete, Response::HTTP_NOT_FOUND);
            }
            if ($request->user()->cannot('destroy', ClubSessionPhoto::class)) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            if ($request->user()->role == RoleEnum::TEACHER->value && $clubSessionPhoto->session->schedule->teacher_id != $request->user()->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN);
            }
            $this->clubSessionPhotoRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::ClubSessionPhotoDelete);
        }
    }
}
