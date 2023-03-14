<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name'=>$this->name,
            'phone'=>$this->phone,
            'birthday'=>$this->birthday,
            'company_id' => $this->company_id,
            'media' => ThumbnailResource::collection($this->getMedia('user_avatar')),
            'email'=>$this->email,
            'faculties' => $this->whenLoaded('faculties', function () {
                return $this->faculties;
            }),
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles;
            })
            ];
    }
}
