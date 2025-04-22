<?php

namespace App\Contracts\Repositories;

use App\Models\Pornstar;
use App\Models\PornstarStat;

interface PornstarStatRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): PornstarStat;
    public function findByPornstarId(int $pornstarId): ?PornstarStat;
}
