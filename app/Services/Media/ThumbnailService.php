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
            
            // If this specific ThumbnailUrl already has a local_path, return it
            if ($thumbnailUrl->local_path && $this->thumbnailExists($thumbnailUrl->local_path)) {
                return $thumbnailUrl->local_path;
            }

            // For duplicate URLs in the same request, we can reuse the downloaded file
            // but we'll create a unique filename for each ThumbnailUrl
            $reuseContent = isset($this->processedUrls[$url]);
            
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $this->filenamePrefix . Str::random(40) . '.' . $extension;

            if (!$reuseContent) {
                $response = $this->httpClient->get($url, ['verify' => false]);
                if (!$response->successful()) {
                    Log::error("Failed to download thumbnail", [
                        'url' => $url,
                        'status' => $response->status()
                    ]);
                    return null;
                }
                
                $this->fileStorage->put($filename, $response->body());
                $this->processedUrls[$url] = $response->body();
            } else {
                // Reuse the content but with a new filename
                $this->fileStorage->put($filename, $this->processedUrls[$url]);
            }

            $thumbnailUrl->update([
                'local_path' => $filename
            ]);

            return $filename;
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
