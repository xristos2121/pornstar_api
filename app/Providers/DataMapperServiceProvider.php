<?php

namespace App\Providers;

use App\Contracts\DataMappers\PornstarMapperInterface;
use App\DataMappers\Pornstar\PornstarMapper;
use Illuminate\Support\ServiceProvider;

class DataMapperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PornstarMapperInterface::class, PornstarMapper::class);
    }
}
