<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PornstarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'name' => $this->name,
            'license' => $this->license,
            'wl_status' => $this->wl_status,
            'link' => $this->link,
            'attributes' => new PornstarAttributeResource($this->whenLoaded('attributes')),
            'stats' => new PornstarStatResource($this->whenLoaded('stats')),
            'hair_colors' => HairColorResource::collection($this->whenLoaded('hairColors')),
            'ethnicities' => EthnicityResource::collection($this->whenLoaded('ethnicities')),
            'aliases' => PornstarAliasResource::collection($this->whenLoaded('aliases')),
            'thumbnails' => ThumbnailResource::collection($this->whenLoaded('thumbnails')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
