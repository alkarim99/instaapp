<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'caption' => $this->caption,
            'total_like' => $this->total_like,
            'total_comment' => $this->total_comment,
            'link' => $this->link,
            'type' => $this->types,
        ];
    }
}
