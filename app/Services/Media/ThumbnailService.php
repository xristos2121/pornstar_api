<?php

namespace App\Services\Media;

use App\Contracts\Services\Http\HttpClientInterface;
use App\Contracts\Services\Media\ThumbnailServiceInterface;
use App\Contracts\Services\Storage\FileStorageInterface;
use App\Models\Thumbnail;
use App\Models\ThumbnailUrl;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThumbnailService implements ThumbnailServiceInterface
{
    protected $filenamePrefix = 'thumbnail_';
    private array $processedUrls = [];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly FileStorageInterface $fileStorage
    ) {}

    public function downloadAndStore(ThumbnailUrl $thumbnailUrl): ?string
    {
        try {
            $url = $thumbnailUrl->url;

            if (isset($this->processedUrls[$url])) {
                if (!$thumbnailUrl->local_path) {
                    $thumbnailUrl->update([
                        'local_path' => $this->processedUrls[$url]
                    ]);
                }
                return $this->processedUrls[$url];
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $this->filenamePrefix . Str::random(40) . '.' . $extension;

            $response = $this->httpClient->get($url, ['verify' => false]);
            if (!$response->successful()) {
                Log::error("Failed to download thumbnail", [
                    'url' => $url,
                    'status' => $response->status()
                ]);
                return null;
            }

            $path = $filename;
            $this->fileStorage->put($path, $response->body());

            $this->processedUrls[$url] = $path;

            $thumbnailUrl->update([
                'local_path' => $path
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error("Error storing thumbnail", [
                'url' => $thumbnailUrl->url ?? null,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getThumbnailPath(string $filename): string
    {
        return $this->fileStorage->path($filename);
    }

    public function thumbnailExists(string $filename): bool
    {
        return $this->fileStorage->exists($filename);
    }

    public function deleteThumbnail(Thumbnail $thumbnail): void
    {
        if ($thumbnail->local_path && $this->thumbnailExists($thumbnail->local_path)) {
            $this->fileStorage->delete($thumbnail->local_path);
            $thumbnail->update(['local_path' => null]);
        }
    }
}
