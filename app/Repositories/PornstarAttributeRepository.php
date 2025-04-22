<?php

namespace App\Repositories;

use App\Contracts\Repositories\PornstarAttributeRepositoryInterface;
use App\Models\PornstarAttribute;

class PornstarAttributeRepository implements PornstarAttributeRepositoryInterface
{
    public function __construct(
        private readonly PornstarAttribute $model
    ) {}

    public function updateOrCreate(array $attributes, array $values): PornstarAttribute
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function findByPornstarId(int $pornstarId): ?PornstarAttribute
    {
        return $this->model->where('pornstar_id', $pornstarId)->first();
    }
}
