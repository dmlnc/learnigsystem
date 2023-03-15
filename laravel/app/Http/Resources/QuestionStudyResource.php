<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionStudyResource extends JsonResource
{
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'thumbnail' => new ImageResource($this->getFirstMedia('question_thumbnail')),
            'question_text' => $this->question_text,
            'position' => $this->position,
            'options' => $this->whenLoaded('options', function () {
               return OptionStudyResource::collection($this->options);
            }),
        ];
    }


}
