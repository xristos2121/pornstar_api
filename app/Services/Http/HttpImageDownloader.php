<?php

namespace App\Services\Http;

use App\Contracts\Services\Http\HttpClientInterface;
use App\Contracts\Services\Media\ImageDownloaderInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class HttpImageDownloader implements ImageDownloaderInterface
{
    protected $timeout;

    protected $validMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        int $timeout = 10
    ) {
        $this->timeout = $timeout;
    }

    public function download(string $url): ?string
    {
        if (!$this->isValidImageUrl($url)) {
            Log::warning("Invalid image URL: {$url}");
            return null;
        }

        try {
            $response = $this->httpClient->get($url, [
                'timeout' => $this->timeout,
                'verify' => false
            ]);

            if (!$response->successful()) {
                Log::warning("Failed to download image: {$url} - Status code: {$response->status()}");
                return null;
            }

            $content = $response->body();
            $mimeType = $this->getImageMimeType($content);

            if (!$mimeType || !in_array($mimeType, $this->validMimeTypes)) {
                Log::warning("Invalid image content type: {$mimeType} for URL: {$url}");
                return null;
            }

            return $content;

        } catch (Exception $e) {
            Log::error("Error downloading image {$url}: " . $e->getMessage());
            return null;
        }

    }

    public function batchDownload(array $urls): array
    {
        $results = [];

        foreach ($urls as $url) {
            if (empty($url)) {
                continue;
            }

            $content = $this->download($url);

            if ($content) {
                $results[$url] = $content;
            }
        }

        return $results;
    }

    public function isValidImageUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        if (!empty($extension) && !in_array(strtolower($extension), $validExtensions)) {
            return false;
        }

        return true;
    }

    public function getImageMimeType(string $imageContent): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($imageContent);
    }

}
