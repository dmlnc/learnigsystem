<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'media' => ThumbnailResource::collection($this->getMedia('question_thumbnail')),
            'points' => $this->points,
            'position' => $this->position,
            'test_id' => $this->test_id,
            'options' => $this->options
        ];
    }
}
