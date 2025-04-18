<?php

namespace App\Repository;

use App\Models\Pornstar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PornstarRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Pornstar;
    public function findByExternalId(int $externalId): ?Pornstar;
    public function create(array $data): Pornstar;
    public function update(Pornstar $pornstar, array $data);
    public function delete(int $id);
}
