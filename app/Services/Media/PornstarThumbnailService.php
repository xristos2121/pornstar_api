<?php

namespace App\Services\Media;

use App\Contracts\Services\Media\ThumbnailServiceInterface;
use App\Contracts\Services\PornstarThumbnailServiceInterface;
use App\Contracts\Repositories\ThumbnailRepositoryInterface;
use App\Contracts\Services\Util\DateTimeProviderInterface;
use App\DTOs\ThumbnailUpdateData;
use App\Models\Pornstar;
use App\Traits\HandlesTransactions;
use Illuminate\Support\Facades\Log;

class PornstarThumbnailService implements PornstarThumbnailServiceInterface
{
    use HandlesTransactions;

    public function __construct(
        private readonly ThumbnailRepositoryInterface $thumbnailRepository,
        private readonly ThumbnailDownloadService $thumbnailDownloadService,
        private readonly ThumbnailServiceInterface $thumbnailService,
        private readonly DateTimeProviderInterface $dateTimeProvider
    ) {}

    private function getCurrentDateTime(): string
    {
        return $this->dateTimeProvider->getCurrentDateTime();
    }

    public function updateThumbnails(Pornstar $pornstar, array $thumbnails): void
    {
        if (empty($thumbnails)) {
            return;
        }

        $now = $this->getCurrentDateTime();

        foreach ($thumbnails as $thumbnailData) {
            try {
                $data = ThumbnailUpdateData::fromObject($thumbnailData);
                $url = !empty($data->urls) ? $data->urls[0] : null;

                if (!$url) continue;

                $this->processThumbnail($pornstar, $data, $url, $now);
            } catch (\Exception $e) {
                Log::warning('Failed to process thumbnail', [
                    'data' => $thumbnailData,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
    }

    private function processThumbnail(Pornstar $pornstar, ThumbnailUpdateData $data, string $url, string $now): void
    {
        $existingUrl = $this->thumbnailRepository->findExistingUrl(
            $pornstar->id,
            $data->type,
            $url
        );

        if ($existingUrl) {
            return;
        }

        $existingThumbnail = $this->thumbnailRepository->findThumbnailByType(
            $pornstar->id,
            $data->type
        );

        if ($existingThumbnail) {
            $this->thumbnailService->deleteThumbnail($existingThumbnail);
            $this->thumbnailRepository->createThumbnailUrl($existingThumbnail, $url);
            return;
        }

        $this->createNewThumbnail($pornstar, $data, $url);
    }

    private function createNewThumbnail(Pornstar $pornstar, ThumbnailUpdateData $data, string $url): void
    {
        $this->executeInTransaction(
            function () use ($pornstar, $data, $url) {
                $thumbnail = $this->thumbnailRepository->createThumbnail([
                    'pornstar_id' => $pornstar->id,
                    'type' => $data->type,
                    'width' => $data->width,
                    'height' => $data->height
                ]);

                $thumbnailUrl = $this->thumbnailRepository->createThumbnailUrl($thumbnail, $url);
                $this->thumbnailDownloadService->downloadAndStoreUrl($thumbnailUrl);

                return $thumbnail;
            },
            ['url' => $url]
        );
    }
}
