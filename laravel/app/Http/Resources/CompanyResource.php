<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'logo' => $this->getFirstMedia('company_logo') 
                                ? new ThumbnailResource($this->getFirstMedia('company_logo')) 
                                : null,
            'name' => $this->name,
        ];
    }
}
