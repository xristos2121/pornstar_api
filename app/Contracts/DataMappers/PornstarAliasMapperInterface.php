<?php

namespace App\Contracts\DataMappers;

use App\Models\PornstarAlias;

interface PornstarAliasMapperInterface
{
    public function mapToModel(array $data): PornstarAlias;
}
