<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'thumbnail' => new ThumbnailResource($this->getFirstMedia('lesson_thumbnail')),
            'title' => $this->title,
            'short_text' => $this->short_text,
            'is_published' => $this->is_published,
            'tests_count' => $this->whenLoaded('tests', function () {
                return $this->tests_count;
            }),
            'finished_tests_count' => $this->whenLoaded('tests', function () {
                return $this->tests->sum('test_finished');
            }),
        ];
    }
}


