<?php

namespace App\Services;

use App\Http\Requests\Comment\CommentCreateRequest;
use App\Http\Resources\Comment\CommentCollectionResource;
use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentService
{
    protected $commentRepository, $postService;

    public function __construct(CommentRepository $commentRepository, PostService $postService)
    {
        $this->commentRepository = $commentRepository;
        $this->postService = $postService;
    }

    public function getCommentsByPost($request)
    {
        $comments = $this->commentRepository->getCommentsByPost($request);
        return new CommentCollectionResource($comments);
    }

    public function storeComment(CommentCreateRequest $commentCreateRequest)
    {
        try {
            DB::beginTransaction();

            $validatedData = $commentCreateRequest->validated();
    
            $validatedData['user_id'] = 1;
    
            $comment = $this->commentRepository->storeComment($validatedData);

            $this->postService->incrementTotalCommentPost($validatedData['post_id'], 1);
    
            DB::commit();

            return $comment;
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function deleteComment(Comment $comment)
    {
        try {
            DB::beginTransaction();

            $this->postService->decrementTotalCommentPost($comment->post_id, 1);

            $this->commentRepository->deleteComment($comment->id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }

    public function deleteCommentByPost(Post $post)
    {
        try {
            DB::beginTransaction();

            $this->commentRepository->deleteCommentByPost($post->id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);
            throw new \Exception($th->getMessage(), 500);
        }
    }
}