<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'media' => $this->whenLoaded('faculties', function () {
               return ThumbnailResource::collection($this->getMedia('course_thumbnail'));
            }),
            'thumbnail' => new ThumbnailResource($this->getFirstMedia('course_thumbnail')),
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => $this->is_published,
            'faculties' => $this->whenLoaded('faculties', function () {
                return $this->faculties;
            }),

        ];
    }
}
