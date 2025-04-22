<?php

namespace App\DTOs;

class PornstarAttributeData
{
    public function __construct(
        public readonly ?string $tattoos = null,
        public readonly ?string $piercings = null,
        public readonly ?string $breast_size = null,
        public readonly ?string $breast_type = null,
        public readonly ?string $orientation = null,
        public readonly ?string $gender = null,
        public readonly ?int $age = null,
        public readonly ?PornstarStatData $stats = null,
        public readonly array $hair_colors = [],
        public readonly array $ethnicities = []
    ) {}

    public static function fromArray(array $data): self
    {
        $hairColors = [];
        if (!empty($data['hairColor'])) {
            $hairColors = array_map(
                fn($color) => new HairColorData(trim($color)),
                explode('|', $data['hairColor'])
            );
        }

        $ethnicities = [];
        if (!empty($data['ethnicity'])) {
            $ethnicities = array_map(
                fn($ethnicity) => new EthnicityData(trim($ethnicity)),
                explode('|', $data['ethnicity'])
            );
        }

        return new self(
            tattoos: $data['tattoos'] ?? null,
            piercings: $data['piercings'] ?? null,
            breast_size: $data['breastSize'] ?? null,
            breast_type: $data['breastType'] ?? null,
            orientation: $data['orientation'] ?? null,
            gender: $data['gender'] ?? null,
            age: isset($data['age']) ? (int)$data['age'] : null,
            stats: isset($data['stats']) ? PornstarStatData::fromArray($data['stats']) : null,
            hair_colors: $hairColors,
            ethnicities: $ethnicities
        );
    }

    public function toArray(): array
    {
        return [
            'tattoos' => $this->tattoos,
            'piercings' => $this->piercings,
            'breast_size' => $this->breast_size,
            'breast_type' => $this->breast_type,
            'orientation' => $this->orientation,
            'gender' => $this->gender,
            'age' => $this->age,
            'stats' => $this->stats?->toArray(),
            'hair_colors' => array_map(fn($color) => $color->toArray(), $this->hair_colors),
            'ethnicities' => array_map(fn($ethnicity) => $ethnicity->toArray(), $this->ethnicities)
        ];
    }
}
