<?php

namespace App\Contracts\Repositories;

interface BaseRepositoryInterface
{
    public function insertOrIgnoreAttributes(array $attributes): void;
    public function getIdsByNames(array $names): array;
    public function syncModelAttributes(object $model, string $relation, array $ids): void;
}
