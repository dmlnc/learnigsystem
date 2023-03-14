<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResourсe extends JsonResource
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
            'title' => $this->title,
            'company_id' => $this->company_id,
            'postable_id'=> $this->postable_id,
            'postable_type'=> $this->postable_type,
            'media' => ThumbnailResource::collection($this->getMedia('post_thumbnail')),
        ];
    }
}
