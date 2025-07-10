<?php

namespace App\Services;

use App\Http\Requests\Post\PostCreateRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Http\Resources\Post\PostCollectionResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use App\Repositories\CommentRepository;
use App\Repositories\LikeRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class PostService
{
    protected $postRepository, $cloudinaryService, $commentRepository, $likeRepository;

    public function __construct(
        PostRepository $postRepository,
        CloudinaryUploadService $cloudinaryService,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository
    ) {
        $this->postRepository = $postRepository;
        $this->cloudinaryService = $cloudinaryService;
        $this->commentRepository = $commentRepository;
        $this->likeRepository = $likeRepository;
    }

    public function getRequest($request)
    {
        return [
            'perPage' => $request->input('limit', 10),
            'page' => $request->input('page', 1),
            'keyword' => $request->input('keyword') ? strtolower($request->input('keyword')) : null,
            'sortBy' => $request->input('sort_by'),
            'orderBy' => $request->input('order_by'),
            'userId' => $request->input('user_id')
        ];
    }

    private function getMeta($posts)
    {
        return [
            'total' => $posts->total(),
            'per_page' => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page' => $posts->lastPage(),
            'from' => $posts->firstItem(),
            'to' => $posts->lastItem()
        ];
    }

    public function getPosts($request)
    {
        $requestParams = $this->getRequest($request);

        $posts = $this->postRepository->getPosts($requestParams);

        $data = $posts ? new PostCollectionResource($posts) : [];

        $meta = $this->getMeta($posts);

        return ['data' => $data, 'meta' => $meta];
    }

    public function getDetailPost($request)
    {
        $post = $this->postRepository->getDetailPost($request);

        return $post == null ? null : new PostResource($post);
    }

    public function storePost(PostCreateRequest $postCreateRequest)
    {
        try {
            $file = $postCreateRequest->file('media');

            $validatedData = $postCreateRequest->safe()->except('media');

            $fileMimeType = $file->getMimeType();
            $fileType = 'image';
            if (str_starts_with($fileMimeType, 'video')) {
                $fileType = 'video';
            }

            if ($fileType === 'image') {
                $uploadedFile = $this->cloudinaryService->uploadImage($file, 'instaapp/posts');
            } else {
                $uploadedFile = $this->cloudinaryService->uploadVideo($file, 'instaapp/posts');
            }

            $fileUrl = $uploadedFile['url'];

            $postData = [
                'user_id' => $postCreateRequest->user()->id,
                'caption' => $validatedData['caption'],
                'link' => $fileUrl,
                'type' => $fileType
            ];

            DB::beginTransaction();

            $post = $this->postRepository->storePost($postData);

            DB::commit();

            return $post;
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function updatePost(PostUpdateRequest $postUpdateRequest, Post $post)
    {
        try {
            $request = $postUpdateRequest;

            $request = [
                'caption' => $request['caption']
            ];

            Log::info($request);

            DB::beginTransaction();

            $this->postRepository->updatePost($request, $post->id);

            DB::commit();

            return $post;
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function deletePost($postId)
    {
        try {
            $post = $this->postRepository->getDetailPost(['id' => $postId]);
            if (!$post) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data not found',
                ], 400);
            }

            $user = auth()->user();
            if ($post->user_id !== $user->id) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }

            DB::beginTransaction();

            $this->commentRepository->deleteCommentByPost($post->id);

            $this->likeRepository->deleteLikeByPost($post->id);

            $this->postRepository->deletePost($post->id);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function incrementTotalCommentPost($post_id, $totalComment)
    {
        return $this->postRepository->incrementTotalCommentPost($post_id, $totalComment);
    }

    public function decrementTotalCommentPost($post_id, $totalComment)
    {
        return $this->postRepository->decrementTotalCommentPost($post_id, $totalComment);
    }

    public function incrementTotalLikePost($post_id, $totalLike)
    {
        return $this->postRepository->incrementTotalLikePost($post_id, $totalLike);
    }

    public function decrementTotalLikePost($post_id, $totalLike)
    {
        return $this->postRepository->decrementTotalLikePost($post_id, $totalLike);
    }
}
