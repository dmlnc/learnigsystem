<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'question_text' => $this->question_text,
            'points' => $this->points,
//            'position' => $this->position,
            'test_id' => $this->test_id,
            'options' => $this->options
        ];
    }
}
