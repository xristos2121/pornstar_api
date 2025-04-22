<?php

namespace App\Contracts\Services\Data;

/**
 * Interface for data processing operations
 */
interface DataProcessorInterface
{
    public function process(array $data): array;

    public function getDataMapper(): DataMapperInterface;

    public function setDataMapper(DataMapperInterface $mapper): self;

    public function validate(array $data): bool;

    public function extractItems(array $data): array;
}
