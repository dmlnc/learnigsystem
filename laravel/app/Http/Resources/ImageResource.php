<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->preview_url,
            'thumbnail' => $this->getUrl('thumbnail'),
            'name' => $this->name,

        ];
    }
}
