<?php

namespace App\Providers;

use App\Contracts\Services\Data\ApiClientInterface;
use App\Contracts\Services\Data\DataProcessorInterface;
use App\Contracts\Services\Http\HttpClientInterface;
use App\Services\Http\HttpApiClient;
use App\Services\Data\PornstarDataProcessor;
use App\DataMappers\Pornstar\PornstarMapper;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiClientInterface::class, function ($app) {
            $url = config('pornstar.api_url');
            $timeout = config('pornstar.api_timeout', 500);

            if (empty($url)) {
                throw new \InvalidArgumentException('API URL is not configured');
            }

            return new HttpApiClient(
                $url, 
                $app->make(HttpClientInterface::class),
                $timeout
            );
        });

        $this->app->singleton(DataProcessorInterface::class, function ($app) {
            return new PornstarDataProcessor($app->make(PornstarMapper::class));
        });
    }
}
