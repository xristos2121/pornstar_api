<?php

namespace App\Contracts\DataMappers;

use App\Models\PornstarAttribute;

interface PornstarAttributeMapperInterface
{
    public function mapToModel(array $data): PornstarAttribute;
}
