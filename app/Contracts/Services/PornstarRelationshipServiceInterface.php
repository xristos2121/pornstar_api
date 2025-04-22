<?php

namespace App\Contracts\Services;

use App\Models\Pornstar;
use App\DTOs\PornstarAttributeData;

interface PornstarRelationshipServiceInterface
{
    public function updateRelationships(Pornstar $pornstar, PornstarAttributeData $data): void;
}
