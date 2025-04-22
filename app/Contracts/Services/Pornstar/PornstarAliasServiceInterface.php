<?php

namespace App\Contracts\Services\Pornstar;

use App\Models\Pornstar;

interface PornstarAliasServiceInterface
{
    public function updateAliases(Pornstar $pornstar, array $aliases): void;
}
