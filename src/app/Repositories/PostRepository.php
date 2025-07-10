<?php

namespace App\Repositories;

use App\Models\Post;

class PostRepository
{
    public function getPosts($request)
    {
        $query = Post::query();
        $keyword = $request['keyword'];
        $sortBy = $request['sortBy'];
        $orderBy = $request['orderBy'];
        $perPage = $request['perPage'];
        $page = $request['page'];
        $userId = $request['userId'];

        if ($keyword) {
            $query->where('caption', 'like', "%{$keyword}%");
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($sortBy) {
            $query->orderBy($sortBy, $orderBy ?? 'asc');
        } else {
            $query->orderBy("created_at", "asc");
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function storePost($request)
    {
        return Post::create($request);
    }

    public function getDetailPost($request)
    {
        return Post::where('id', $request['id'])->first();
    }

    public function updatePost($request, $id)
    {
        return Post::where('id', $id)->update($request);
    }

    public function deletePost($id)
    {
        return Post::where('id', $id)->delete();
    }

    public function incrementTotalCommentPost($id, $totalComment)
    {
        return Post::where('id', $id)->increment('total_comment', $totalComment);
    }

    public function decrementTotalCommentPost($id, $totalComment)
    {
        return Post::where('id', $id)->decrement('total_comment', $totalComment);
    }

    public function incrementTotalLikePost($id, $totalLike)
    {
        return Post::where('id', $id)->increment('total_like', $totalLike);
    }

    public function decrementTotalLikePost($id, $totalLike)
    {
        return Post::where('id', $id)->decrement('total_like', $totalLike);
    }
}