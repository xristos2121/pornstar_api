<?php

namespace App\Contracts\Services\Data;

interface ApiClientInterface
{
    public function fetch(): array;
    public function isSourceAvailable(): bool;
}
