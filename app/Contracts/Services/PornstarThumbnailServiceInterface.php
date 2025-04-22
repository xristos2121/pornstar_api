<?php

namespace App\Contracts\Services;

use App\Models\Pornstar;

interface PornstarThumbnailServiceInterface
{
    public function updateThumbnails(Pornstar $pornstar, array $thumbnails): void;
}
