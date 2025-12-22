<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'content'=> $this->content,
            'author'=>new UserResource($this->user),
            'ad_id'=> $this->ad_id,
            'created_at'=>$this->created_at,
        ];
    }
}
