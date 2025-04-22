<?php

namespace App\DataMappers\Attributes;

use App\Models\Ethnicity;

class EthnicityMapper
{
    public function mapToModel(array $data): Ethnicity
    {
        return Ethnicity::firstOrCreate([
            'name' => $data['name']
        ]);
    }
}
