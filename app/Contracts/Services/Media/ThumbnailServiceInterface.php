<?php

namespace App\Contracts\Services\Media;

use App\Models\Thumbnail;
use App\Models\ThumbnailUrl;

interface ThumbnailServiceInterface
{
    public function downloadAndStore(ThumbnailUrl $thumbnailUrl): ?string;
    public function getThumbnailPath(string $filename): string;
    public function thumbnailExists(string $filename): bool;
    public function deleteThumbnail(Thumbnail $thumbnail): void;
}
