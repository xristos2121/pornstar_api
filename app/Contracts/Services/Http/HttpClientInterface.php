<?php

namespace App\Contracts\Services\Http;

interface HttpClientInterface
{
    public function get(string $url, array $options = []): HttpResponseInterface;
    public function head(string $url, array $options = []): HttpResponseInterface;
}
