<?php

namespace App\Repositories;

use App\Contracts\Repositories\PornstarStatRepositoryInterface;
use App\Models\PornstarStat;

class PornstarStatRepository implements PornstarStatRepositoryInterface
{
    public function __construct(
        private readonly PornstarStat $model
    ) {}

    public function updateOrCreate(array $attributes, array $values): PornstarStat
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function findByPornstarId(int $pornstarId): ?PornstarStat
    {
        return $this->model->where('pornstar_id', $pornstarId)->first();
    }
}
