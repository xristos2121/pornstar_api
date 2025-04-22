<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PornstarAliasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'alias' => $this->alias
        ];
    }
}
