<?php

namespace App\Contracts\Services\Media;

/**
 * Interface for image downloading
 */
interface ImageDownloaderInterface
{
    public function download(string $url): ?string;
    public function batchDownload(array $urls): array;
    public function isValidImageUrl(string $url): bool;
    public function getImageMimeType(string $imageContent): ?string;
}
