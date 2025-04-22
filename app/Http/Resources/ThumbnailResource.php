<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThumbnailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Get the host
        $host = $request->getSchemeAndHttpHost();
        
        // Get the first URL if available
        $url = null;
        if ($this->relationLoaded('urls') && $this->urls->count() > 0) {
            $firstUrl = $this->urls->first();
            if ($firstUrl->local_path) {
                $url = $host . '/storage/thumbnails/' . $firstUrl->local_path;
            } else {
                $url = $firstUrl->url;
            }
        }
        
        return [
            'height' => $this->height,
            'width' => $this->width,
            'type' => $this->type,
            'url' => $url,
            'urls' => ThumbnailUrlResource::collection($this->whenLoaded('urls')),
        ];
    }
}
