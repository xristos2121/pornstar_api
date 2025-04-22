<?php

namespace App\Contracts\Repositories;

use App\Models\Pornstar;
use App\Models\PornstarAttribute;

interface PornstarAttributeRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): PornstarAttribute;
    public function findByPornstarId(int $pornstarId): ?PornstarAttribute;
}
