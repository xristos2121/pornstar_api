<?php

namespace App\Services\Http;

use App\Contracts\Services\Data\ApiClientInterface;
use App\Contracts\Services\Http\HttpClientInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class HttpApiClient implements ApiClientInterface
{
    private const DEFAULT_TIMEOUT = 500;

    protected $timeout = self::DEFAULT_TIMEOUT;
    protected $sourceUrl;
    protected $headers = [];

    public function __construct(
        string $sourceUrl, 
        private readonly HttpClientInterface $httpClient,
        int $timeout = 30
    ) {
        $this->sourceUrl = $sourceUrl;
        $this->timeout = $timeout;
        ini_set('memory_limit', '256M');
    }

    public function fetch(): array
    {
        try {
            $response = $this->httpClient->get($this->sourceUrl, [
                'timeout' => $this->timeout,
                'verify' => false
            ]);

            if (!$response->successful()) {
                throw new Exception("API request failed with status: {$response->status()}");
            }

            $body = (string) $response->body();
            $cleaned = preg_replace('/\\\\x[0-9A-Fa-f]{2}/', '', $body);

            $data = json_decode($cleaned, true, 512, JSON_THROW_ON_ERROR);

            return [
                'data' => $data,
                'status' => 200,
                'success' => true,
            ];
        } catch (Exception $e) {
            Log::error('HttpApiClient fetch error: ' . $e->getMessage());

            return [
                'data' => null,
                'status' => 500,
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function isSourceAvailable(): bool
    {
        try {
            $response = $this->httpClient->head($this->sourceUrl, [
                'timeout' => 5,
                'verify' => false,
                'headers' => $this->headers
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::warning("Source {$this->sourceUrl} is not available: " . $e->getMessage());
            return false;
        }
    }
}
