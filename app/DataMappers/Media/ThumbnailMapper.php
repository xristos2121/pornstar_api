<?php

namespace App\DataMappers\Media;

use App\Contracts\DataMappers\ThumbnailMapperInterface;
use App\Models\Thumbnail;
use App\Services\Media\ThumbnailService;
use App\DTOs\ThumbnailData;

class ThumbnailMapper implements ThumbnailMapperInterface
{
    public function __construct(
        private readonly ThumbnailService $thumbnailService
    ) {}

    public function mapToModel(array $data): Thumbnail
    {
        $thumbnailData = ThumbnailData::fromArray($data);

        $thumbnail = Thumbnail::firstOrCreate([
            'pornstar_id' => $data['pornstar_id'],
            'width' => $thumbnailData->width,
            'height' => $thumbnailData->height,
            'type' => $thumbnailData->type ?? 'pc',
        ]);

        foreach ($thumbnailData->urls as $url) {
            if (empty($url)) continue;

            $thumbnail->urls()->create([
                'url' => $url,
            ]);
        }

        $thumbnail->load('urls');

        $this->thumbnailService->downloadAndStore($thumbnail);

        return $thumbnail;
    }
}
