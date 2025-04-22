<?php

namespace App\Contracts\DataMappers;

use App\Models\Thumbnail;

interface ThumbnailMapperInterface
{
    public function mapToModel(array $data): Thumbnail;
}
