<?php

namespace App\Contracts\Services\Http;

interface HttpResponseInterface
{
    public function successful(): bool;
    public function status(): int;
    public function body(): string;
}
