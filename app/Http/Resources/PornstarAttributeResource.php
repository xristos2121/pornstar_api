<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PornstarAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'tattoos' => $this->tattoos,
            'piercings' => $this->piercings,
            'breast_size' => $this->breast_size,
            'breast_type' => $this->breast_type,
            'orientation' => $this->orientation,
            'gender' => $this->gender,
            'age' => $this->age,
        ];
    }
}
