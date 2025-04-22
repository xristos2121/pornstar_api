<?php

namespace App\Providers;

use App\Contracts\Services\Http\HttpClientInterface;
use App\Contracts\Services\Media\ThumbnailServiceInterface;
use App\Contracts\Services\Storage\FileStorageInterface;
use App\Contracts\Services\Util\DateTimeProviderInterface;
use App\Services\Http\LaravelHttpClient;
use App\Services\Media\ThumbnailDownloadService;
use App\Services\Media\ThumbnailService;
use App\Services\Storage\LaravelFileStorage;
use App\Services\Util\DateTimeProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(HttpClientInterface::class, LaravelHttpClient::class);
        $this->app->bind(FileStorageInterface::class, function ($app) {
            return new LaravelFileStorage('thumbnails');
        });
        $this->app->bind(ThumbnailServiceInterface::class, ThumbnailService::class);
        $this->app->bind(DateTimeProviderInterface::class, DateTimeProvider::class);
        
        // Register singletons
        $this->app->singleton(ThumbnailDownloadService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
