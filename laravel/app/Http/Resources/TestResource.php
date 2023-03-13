<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'thumbnail' => new ThumbnailResource($this->getFirstMedia('test_thumbnail')),
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => $this->is_published,
            'test_results' => $this->whenLoaded('test_results', function () {
               return $this->test_results;
            }),
        ];
    }


}
