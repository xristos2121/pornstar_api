<?php

namespace App\DTOs;

class PornstarAliasData
{
    public function __construct(
        public readonly string $name
    ) {}

    public static function fromArray(string|array $data): self
    {
        if (is_string($data)) {
            return new self(name: $data);
        }
        
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
