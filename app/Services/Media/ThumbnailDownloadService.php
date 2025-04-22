<?php

namespace App\Services\Media;

use App\Contracts\Services\Media\ThumbnailServiceInterface;
use App\Models\ThumbnailUrl;

class ThumbnailDownloadService
{
    private array $urlCache = [];

    public function __construct(
        private readonly ThumbnailServiceInterface $thumbnailService
    ) {}

    public function downloadAndStoreUrl(ThumbnailUrl $thumbnailUrl): void
    {
        $url = $thumbnailUrl->url;

        if (isset($this->urlCache[$url])) {
            $thumbnailUrl->update(['cached_path' => $this->urlCache[$url]]);
            return;
        }

        $cachedPath = $this->thumbnailService->downloadAndStore($thumbnailUrl);
        if ($cachedPath) {
            $this->urlCache[$url] = $cachedPath;
            $thumbnailUrl->update(['cached_path' => $cachedPath]);
        }
    }
}
