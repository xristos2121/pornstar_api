<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThumbnailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'height' => $this->height,
            'width' => $this->width,
            'type' => $this->type,
            'urls' => ThumbnailUrlResource::collection($this->whenLoaded('urls')),
        ];
    }
}
