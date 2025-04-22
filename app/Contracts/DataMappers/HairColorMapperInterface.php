<?php

namespace App\Contracts\DataMappers;

use App\Models\HairColor;

interface HairColorMapperInterface
{
    public function mapToModel(array $data): HairColor;
}
