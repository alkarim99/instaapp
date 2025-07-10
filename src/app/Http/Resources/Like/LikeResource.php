<?php

namespace App\Http\Resources\Like;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'photo_profile' => $this->user->photo_profile
            ],
            'post' => [
                'id' => $this->post->id,
                'user' => [
                    'id' => $this->post->user->id,
                    'name' => $this->post->user->name
                ]
            ]
        ];
    }
}
