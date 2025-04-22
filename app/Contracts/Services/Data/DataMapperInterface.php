<?php

namespace App\Contracts\Services\Data;

/**
 * Interface for mapping data between different formats
 */
interface DataMapperInterface
{
    public function map(array $item): array;

    public function getAttributeProcessors(): array;

    public function registerProcessor(string $attributeName, $processor): self;

    public function getAttributeMapping(string $sourceAttribute): ?string;
}
