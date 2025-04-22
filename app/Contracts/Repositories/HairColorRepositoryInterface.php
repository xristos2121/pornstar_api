<?php

namespace App\Contracts\Repositories;

use App\Models\Pornstar;
use App\Models\HairColor;

interface HairColorRepositoryInterface extends BaseRepositoryInterface
{
    public function findOrCreateByName(string $name): ?HairColor;
    public function findByNames(array $names): array;
}
