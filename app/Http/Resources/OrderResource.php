<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
                'ad'=>new AdResource($this->ad),
                'buyer'=>new UserResource($this->buyer),
                'seller'=>new UserResource($this->seller),
                'price'=>$this->price,
                'status'=>$this->status,
                'created_at'=>$this->created_at,
        ];
    }
}
