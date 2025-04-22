<?php

namespace App\DataMappers\Pornstar;

use App\Contracts\DataMappers\PornstarAttributeMapperInterface;
use App\Models\PornstarAttribute;

class PornstarAttributeMapper implements PornstarAttributeMapperInterface
{
    public function mapToModel(array $data): PornstarAttribute
    {
        $attributes = [
            'pornstar_id' => $data['pornstar_id'],
            'tattoos' => $this->toBool($data['tattoos'] ?? null),
            'piercings' => $this->toBool($data['piercings'] ?? null),
            'breast_size' => $data['breast_size'] ?? null,
            'breast_type' => $data['breast_type'] ?? null,
            'orientation' => $data['orientation'] ?? null,
            'gender' => $data['gender'] ?? null,
            'age' => isset($data['age']) ? (int)$data['age'] : null,
        ];

        if (isset($data['created_at'])) {
            $attributes['created_at'] = $data['created_at'];
        }
        if (isset($data['updated_at'])) {
            $attributes['updated_at'] = $data['updated_at'];
        }

        return PornstarAttribute::create($attributes);
    }

    private function toBool($value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            if ($lower === 'true' || $lower === '1' || $lower === 'yes') {
                return true;
            }
            if ($lower === 'false' || $lower === '0' || $lower === 'no') {
                return false;
            }
        }

        return (bool)$value;
    }
}
