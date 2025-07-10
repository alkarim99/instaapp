<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostCreateRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $result = $this->postService->getPosts($request);

        return response()->json([
            'status' => 200,
            'message' => 'Success get data!',
            'data' => $result['data'],
            'meta' => $result['meta']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $postCreateRequest)
    {
        $post = $this->postService->storePost($postCreateRequest);

        return response()->json([
            'status' => 201,
            'message' => 'Post created successfully',
            'data' => $post
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = $this->postService->getDetailPost(['id' => $id]);
        return response()->json([
            'status' => 200,
            'message' => 'Success get data!',
            'data' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $postUpdateRequest, Post $post)
    {
        $postData = $this->postService->updatePost($postUpdateRequest, $post);

        return response()->json([
            'status' => 200,
            'message' => 'Post updated successfully',
            'data' => $postData
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId)
    {
        return $this->postService->deletePost($postId);
    }
}
