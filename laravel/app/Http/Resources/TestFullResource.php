<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestFullResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'media' => ThumbnailResource::collection($this->getMedia('test_thumbnail')),
            'title' => $this->title,
            'description' => $this->description,
            'position' => $this->position,
            'is_published' => $this->is_published,
            'questions' => QuestionResource::collection($this->questions)
        ];
    }
}


