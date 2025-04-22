<?php

namespace App\Contracts\Repositories;

use App\Models\Pornstar;
use App\Models\Thumbnail;
use App\Models\ThumbnailUrl;

interface ThumbnailRepositoryInterface
{
    public function findExistingUrl(int $pornstarId, string $type, string $url): ?ThumbnailUrl;
    public function findThumbnailByType(int $pornstarId, string $type): ?Thumbnail;
    public function createThumbnail(array $data): Thumbnail;
    public function updateThumbnail(Thumbnail $thumbnail, array $data): bool;
    public function createThumbnailUrl(Thumbnail $thumbnail, string $url): ThumbnailUrl;
}
