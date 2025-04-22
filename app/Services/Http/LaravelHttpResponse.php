<?php

namespace App\Services\Http;

use App\Contracts\Services\Http\HttpResponseInterface;
use Illuminate\Http\Client\Response;

class LaravelHttpResponse implements HttpResponseInterface
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function successful(): bool
    {
        return $this->response->successful();
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function body(): string
    {
        return $this->response->body();
    }
}
