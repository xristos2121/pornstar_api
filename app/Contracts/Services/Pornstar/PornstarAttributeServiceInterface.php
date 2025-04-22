<?php

namespace App\Contracts\Services\Pornstar;

use App\Models\Pornstar;
use App\DTOs\PornstarAttributeData;

interface PornstarAttributeServiceInterface
{
    public function updateAttributes(Pornstar $pornstar, PornstarAttributeData $data): void;
}
