<?php

namespace App\Contracts\DataMappers;

use App\Models\Pornstar;

interface PornstarMapperInterface
{
    public function mapToModel(array $data): Pornstar;

    public function mapManyToModel(array $dataArray): array;

    public function mapFromModel(Pornstar $pornstar): array;

    public function mapManyFromModel(array $pornstars): array;
}
