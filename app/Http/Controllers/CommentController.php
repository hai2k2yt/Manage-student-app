<?php

namespace App\Http\Controllers;

use App\Enums\ErrorCodeEnum;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Repositories\CommentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /**
     * @param CommentRepository $commentRepository
     */
    public function __construct(protected CommentRepository $commentRepository)
    {
    }

    public function all(): JsonResponse
    {
        $comments = $this->commentRepository->getAll();
        $records = CommentResource::collection($comments);
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
        $comments = $this->commentRepository->getCommentList($conditions);
        return $this->sendPaginationResponse($comments, CommentResource::collection($comments));
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $comment = $this->commentRepository->create($requestData);
            $commentResource = new CommentResource($comment);
            DB::commit();
            return $this->sendResponse($commentResource, __('common.created'), Response::HTTP_CREATED);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::CommentStore);
        }
    }

    /**
     * Update corporation department.
     *
     * @param string $id
     * @param UpdateCommentRequest $request
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->validated();
            $comment = $this->commentRepository->find($id);
            if (!$comment) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::CommentUpdate, Response::HTTP_NOT_FOUND);
            }

            $comment = $this->commentRepository->update($id, $requestData);
            $commentResource = new CommentResource($comment);
            DB::commit();
            return $this->sendResponse($commentResource, __('common.updated'));
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::CommentUpdate);
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
            $comment = $this->commentRepository->find($id);
            if (!$comment) {
                return $this->sendError(__('common.not_found'), ErrorCodeEnum::CommentDelete, Response::HTTP_NOT_FOUND);
            }
            $this->commentRepository->delete($id);
            DB::commit();
            return $this->sendResponse(null, __('common.deleted'), Response::HTTP_NO_CONTENT);
        } catch (Exception $error) {
            DB::rollBack();
            return $this->sendExceptionError($error, ErrorCodeEnum::CommentDelete);
        }
    }
}
