<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThumbnailUrlResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $host = $request->getSchemeAndHttpHost();
        $fullUrl = $this->local_path
            ? $host . '/storage/thumbnails/' . $this->local_path
            : $this->url;

        return [
            'url' => $fullUrl
        ];
    }
}
