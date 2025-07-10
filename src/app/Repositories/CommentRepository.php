<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    public function getCommentsByPost($request)
    {
        $comment = Comment::where('post_id', $request['id'])->get();
        return $comment;
    }

    public function storeComment($request)
    {
        return Comment::create($request);
    }

    public function deleteComment($id)
    {
        return Comment::where('id', $id)->delete();
    }
}