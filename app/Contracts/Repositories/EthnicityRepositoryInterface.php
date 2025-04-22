<?php

namespace App\Contracts\Repositories;

use App\Models\Pornstar;
use App\Models\Ethnicity;

interface EthnicityRepositoryInterface extends BaseRepositoryInterface
{
    public function findOrCreateByName(string $name): ?Ethnicity;
    public function findByNames(array $names): array;
}
