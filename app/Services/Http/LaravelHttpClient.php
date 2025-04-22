<?php

namespace App\Services\Http;

use App\Contracts\Services\Http\HttpClientInterface;
use App\Contracts\Services\Http\HttpResponseInterface;
use Illuminate\Support\Facades\Http;

class LaravelHttpClient implements HttpClientInterface
{
    public function get(string $url, array $options = []): HttpResponseInterface
    {
        $response = Http::withOptions($options)->get($url);
        return new LaravelHttpResponse($response);
    }
    public function head(string $url, array $options = []): HttpResponseInterface
    {
        $headers = $options['headers'] ?? [];
        unset($options['headers']);

        $response = Http::withOptions($options)
            ->withHeaders($headers)
            ->head($url);

        return new LaravelHttpResponse($response);
    }
}
