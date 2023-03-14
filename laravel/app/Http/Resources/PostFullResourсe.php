<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostFullResourсe extends JsonResource
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
            'text' => $this->text,
            'postable_id'=> $this->postable_id,
            'postable_type'=> $this->postable_type,
            'company_id' => $this->company_id,
            'media' => ThumbnailResource::collection($this->getMedia('post_thumbnail')),
        ];
    }
}
