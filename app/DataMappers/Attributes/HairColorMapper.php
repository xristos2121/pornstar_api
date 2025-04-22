<?php

namespace App\DataMappers\Attributes;

use App\Contracts\DataMappers\HairColorMapperInterface;
use App\Models\HairColor;

class HairColorMapper implements HairColorMapperInterface
{
    public function mapToModel(array $data): HairColor
    {
        return HairColor::firstOrCreate([
            'name' => $data['name']
        ]);
    }
}
