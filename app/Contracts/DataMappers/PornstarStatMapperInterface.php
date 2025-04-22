<?php

namespace App\Contracts\DataMappers;

use App\Models\PornstarStat;

interface PornstarStatMapperInterface
{
    public function mapToModel(array $data): PornstarStat;
}
