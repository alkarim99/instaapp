<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentCreateRequest;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    public function indexByPost(Request $request)
    {
        $comments = $this->commentService->getCommentsByPost($request);

        return response()->json([
            'status' => 201,
            'message' => 'Success get data!',
            'data' => $comments
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
    public function store(CommentCreateRequest $commentCreateRequest)
    {
        $comment = $this->commentService->storeComment($commentCreateRequest);

        return response()->json([
            'status' => 201,
            'message' => 'Content created successfully',
            'data' => $comment
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->commentService->deleteComment($comment->id);

        return response()->json([
            'status' => 200,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
