<?php

namespace App\Contracts\DataMappers;

use App\Models\Ethnicity;

interface EthnicityMapperInterface
{
    public function mapToModel(array $data): Ethnicity;
}
