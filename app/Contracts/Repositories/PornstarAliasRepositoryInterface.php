<?php

namespace App\Contracts\Repositories;

use App\Models\Pornstar;
use Illuminate\Support\Collection;

interface PornstarAliasRepositoryInterface
{
    public function getByPornstarId(int $pornstarId): Collection;
    public function deleteAliases(int $pornstarId, array $aliasNames): void;
    public function createAliases(array $aliasRecords): void;
}
