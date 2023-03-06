<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThumbnailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'preview_url' => $this->preview_url,
            'url' => $this->getUrl('thumbnail'),
            'name' => $this->name,

        ];
    }
}
