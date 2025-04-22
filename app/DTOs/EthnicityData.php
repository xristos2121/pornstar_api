<?php

namespace App\DTOs;

class EthnicityData
{
    public function __construct(
        public readonly string $name
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
