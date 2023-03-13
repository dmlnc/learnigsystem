<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonStudyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'media' => ImageResource::collection($this->getMedia('lesson_thumbnail')),
            'title' => $this->title,
            'video' => $this->video,
            'short_text' => $this->short_text,
            'long_text' => $this->long_text,
            'tests' => $this->whenLoaded('tests', function () {
               return TestResource::collection($this->tests);
            }),

        ];
    }
}


