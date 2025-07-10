<?php

namespace App\Http\Controllers;

use App\Http\Requests\Like\LikeCreateRequest;
use App\Models\Like;
use App\Services\LikeService;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function indexByPost(Request $request)
    {
        $likes = $this->likeService->getLikesByPost($request);

        return response()->json([
            'status' => 201,
            'message' => 'Success get data!',
            'data' => $likes
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
    public function store(LikeCreateRequest $likeCreateRequest)
    {
        $like = $this->likeService->storeLike($likeCreateRequest);

        return response()->json([
            'status' => 201,
            'message' => 'Like created successfully',
            'data' => $like
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($likeId)
    {
        return $this->likeService->deleteLike($likeId);
    }
}
