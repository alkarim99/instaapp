<?php

namespace App\Services;

use App\Http\Requests\Post\PostCreateRequest;
use App\Http\Resources\Post\PostCollectionResource;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostService
{
    protected $postRepository, $cloudinaryService;

    public function __construct(PostRepository $postRepository, CloudinaryUploadService $cloudinaryService)
    {
        $this->postRepository = $postRepository;
        $this->cloudinaryService = $cloudinaryService;
    }

    public function getRequest($request)
    {
        return [
            'perPage' => $request->input('limit', 10),
            'page' => $request->input('page', 1),
            'keyword' => $request->input('keyword') ? strtolower($request->input('keyword')) : null,
            'sortBy' => $request->input('sort_by'),
            'orderBy' => $request->input('order_by'),
            'user_id' => $request->input('user_id')
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
                'user_id' => 1,
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
}
