<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'media' => ThumbnailResource::collection($this->getMedia('course_thumbnail')),
            'title' => $this->title,
            'video' => $this->video,
            'short_text' => $this->short_text,
            'long_text' => $this->long_text,
            'position' => $this->position,
            'is_published' => $this->is_published,
        ];
    }
}


