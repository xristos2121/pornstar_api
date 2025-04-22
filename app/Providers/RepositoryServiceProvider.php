<?php

namespace App\Providers;

use App\Contracts\Repositories\ThumbnailRepositoryInterface;
use App\Contracts\Repositories\HairColorRepositoryInterface;
use App\Contracts\Repositories\EthnicityRepositoryInterface;
use App\Contracts\Repositories\PornstarAttributeRepositoryInterface;
use App\Contracts\Repositories\PornstarStatRepositoryInterface;
use App\Contracts\Repositories\PornstarAliasRepositoryInterface;
use App\Contracts\Services\Util\DateTimeProviderInterface;
use App\Repositories\ThumbnailRepository;
use App\Repositories\HairColorRepository;
use App\Repositories\EthnicityRepository;
use App\Repositories\PornstarAttributeRepository;
use App\Repositories\PornstarStatRepository;
use App\Repositories\PornstarAliasRepository;
use App\Services\Util\DateTimeProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ThumbnailRepositoryInterface::class, ThumbnailRepository::class);
        $this->app->bind(PornstarAttributeRepositoryInterface::class, PornstarAttributeRepository::class);
        $this->app->bind(PornstarStatRepositoryInterface::class, PornstarStatRepository::class);
        $this->app->bind(DateTimeProviderInterface::class, DateTimeProvider::class);
        $this->app->bind(HairColorRepositoryInterface::class, HairColorRepository::class);
        $this->app->bind(EthnicityRepositoryInterface::class, EthnicityRepository::class);
        $this->app->bind(PornstarAliasRepositoryInterface::class, PornstarAliasRepository::class);
    }
}
