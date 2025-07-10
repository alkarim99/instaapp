<?php

namespace App\Services;

use App\Http\Requests\Like\LikeCreateRequest;
use App\Http\Resources\Like\LikeCollectionResource;
use App\Models\Like;
use App\Models\Post;
use App\Repositories\LikeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LikeService
{
    protected $likeRepository, $postService;

    public function __construct(LikeRepository $likeRepository, PostService $postService)
    {
        $this->likeRepository = $likeRepository;
        $this->postService = $postService;
    }

    public function getLikesByPost($request)
    {
        $likes = $this->likeRepository->getLikesByPost($request);
        return new LikeCollectionResource($likes);
    }

    public function storeLike(LikeCreateRequest $likeCreateRequest)
    {
        try {
            $validatedData = $likeCreateRequest->validated();

            $validatedData['user_id'] = $likeCreateRequest->user()->id;

            DB::beginTransaction();

            $like = $this->likeRepository->storeLike($validatedData);

            $this->postService->incrementTotalLikePost($validatedData['post_id'], 1);

            DB::commit();

            return $like;
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function deleteLike($likeId)
    {
        try {
            $like = $this->likeRepository->getDetailLike($likeId);
            if (!$like) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data not found',
                ], 400);
            }

            $user = auth()->user();
            if ($like->user_id !== $user->id) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            DB::beginTransaction();

            $this->postService->decrementTotalLikePost($like->post_id, 1);

            $this->likeRepository->deleteLike($like->id);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Like deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function deleteLikeByPost(Post $post)
    {
        try {
            DB::beginTransaction();

            $this->likeRepository->deleteLikeByPost($post->id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }
}
