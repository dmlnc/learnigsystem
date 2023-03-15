<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionStudyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'option_text' => $this->option_text,
        ];
    }


}
