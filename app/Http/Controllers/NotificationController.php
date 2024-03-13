<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Repositories\NotificationRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(protected NotificationRepository $notificationRepository)
    {
    }

    public function all(): JsonResponse
    {
        $notifications = $this->notificationRepository->getAll();
        $records = NotificationResource::collection($notifications);
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
        $notifications = $this->notificationRepository->getNotificationList($conditions);
        return $this->sendPaginationResponse($notifications, NotificationResource::collection($notifications));
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $notification = $this->notificationRepository->create($requestData);
            $notificationResource = new NotificationResource($notification);
            DB::commit();
            return $this->sendResponse($notificationResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::NotificationStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateNotificationRequest $request
     * @return JsonResponse
     */
    public function update(UpdateNotificationRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $notification = $this->notificationRepository->find($id);
            if (!$notification) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::NotificationUpdate, Response::HTTP_NOT_FOUND);
            }

            $notification = $this->notificationRepository->update($id, $requestData);
            $notificationResource = new NotificationResource($notification);
            DB::commit();
            return $this->sendResponse($notificationResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::NotificationUpdate);
        }
    }

    /**
     * Delete corporation department.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $comment = $this->notificationRepository->find($id);
            if (!$comment) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::NotificationDelete, Response::HTTP_NOT_FOUND);
            }
            $this->notificationRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::NotificationDelete);
        }
    }
}
