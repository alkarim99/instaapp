<?php

namespace App\Repositories;

use App\Models\Like;

class LikeRepository
{
    public function getLikesByPost($request)
    {
        $like = Like::where('post_id', $request['id'])->get();
        return $like;
    }

    public function getDetailLike($likeId)
    {
        return Like::where('id', $likeId)->first();
    }

    public function getLikeByPostAndUser($postId, $userId)
    {
        return Like::where('post_id', $postId)->where('user_id', $userId)->first();
    }

    public function storeLike($request)
    {
        return Like::create($request);
    }

    public function deleteLike($id)
    {
        return Like::where('id', $id)->delete();
    }

    public function deleteLikeByPost($postId)
    {
        return Like::where('post_id', $postId)->delete();
    }
}