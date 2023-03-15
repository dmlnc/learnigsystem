<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestStudyResource extends JsonResource
{
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'thumbnail' => new ImageResource($this->getFirstMedia('test_thumbnail')),
            'title' => $this->title,

            'description' => $this->description,
            'questions' => $this->whenLoaded('questions', function () {
               return QuestionStudyResource::collection($this->questions);
            }),
        ];
    }


}
