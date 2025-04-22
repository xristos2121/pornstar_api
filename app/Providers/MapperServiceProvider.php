<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\DataMappers\PornstarMapperInterface;
use App\Contracts\DataMappers\PornstarAliasMapperInterface;
use App\Contracts\DataMappers\PornstarAttributeMapperInterface;
use App\Contracts\DataMappers\ThumbnailMapperInterface;
use App\Contracts\DataMappers\HairColorMapperInterface;
use App\Contracts\DataMappers\EthnicityMapperInterface;
use App\Contracts\DataMappers\PornstarStatMapperInterface;
use App\DataMappers\Pornstar\PornstarMapper;
use App\DataMappers\Pornstar\PornstarAliasMapper;
use App\DataMappers\Pornstar\PornstarAttributeMapper;
use App\DataMappers\Pornstar\PornstarStatMapper;
use App\DataMappers\Media\ThumbnailMapper;
use App\DataMappers\Attributes\HairColorMapper;
use App\DataMappers\Attributes\EthnicityMapper;

class MapperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PornstarMapperInterface::class, PornstarMapper::class);
        $this->app->bind(PornstarAliasMapperInterface::class, PornstarAliasMapper::class);
        $this->app->bind(PornstarAttributeMapperInterface::class, PornstarAttributeMapper::class);
        $this->app->bind(PornstarStatMapperInterface::class, PornstarStatMapper::class);
        $this->app->bind(ThumbnailMapperInterface::class, ThumbnailMapper::class);
        $this->app->bind(HairColorMapperInterface::class, HairColorMapper::class);
        $this->app->bind(EthnicityMapperInterface::class, EthnicityMapper::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
