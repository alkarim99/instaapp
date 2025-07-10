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
        $userId = $request['user_id'];

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
}