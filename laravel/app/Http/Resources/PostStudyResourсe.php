<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostStudyResourсe extends JsonResource
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

            'date' => $this->created_at->format('d.m.y'),
            'media' => new ImageResource($this->getFirstMedia('post_thumbnail')),
        ];
    }
}
