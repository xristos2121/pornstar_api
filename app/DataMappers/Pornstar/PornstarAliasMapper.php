<?php

namespace App\DataMappers\Pornstar;

use App\Contracts\DataMappers\PornstarAliasMapperInterface;
use App\Models\PornstarAlias;

class PornstarAliasMapper implements PornstarAliasMapperInterface
{
    public function mapToModel(array $data): PornstarAlias
    {
        return PornstarAlias::firstOrCreate([
            'pornstar_id' => $data['pornstar_id'],
            'alias' => $data['name']
        ]);
    }
}
