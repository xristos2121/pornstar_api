<?php

namespace App\Repositories;

use App\Contracts\Repositories\ThumbnailRepositoryInterface;
use App\Models\Thumbnail;
use App\Models\ThumbnailUrl;
use Illuminate\Support\Facades\DB;

class ThumbnailRepository implements ThumbnailRepositoryInterface
{
    public function __construct(
        private readonly ThumbnailUrl $thumbnailUrlModel,
        private readonly Thumbnail $thumbnailModel
    ) {}

    public function findExistingUrl(int $pornstarId, string $type, string $url): ?ThumbnailUrl
    {
        return $this->thumbnailUrlModel
            ->whereHas('thumbnail', function($query) use ($pornstarId, $type) {
                $query->where('pornstar_id', $pornstarId)
                      ->where('type', $type);
            })
            ->where('url', $url)
            ->first();
    }

    public function findThumbnailByType(int $pornstarId, string $type): ?Thumbnail
    {
        return $this->thumbnailModel
            ->where('pornstar_id', $pornstarId)
            ->where('type', $type)
            ->first();
    }

    public function createThumbnail(array $data): Thumbnail
    {
        return $this->thumbnailModel->create($data);
    }

    public function updateThumbnail(Thumbnail $thumbnail, array $data): bool
    {
        return $thumbnail->update($data);
    }

    public function createThumbnailUrl(Thumbnail $thumbnail, string $url): ThumbnailUrl
    {
        return $thumbnail->urls()->create(['url' => $url]);
    }
}
