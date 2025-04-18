<?php

namespace App\Repositories\Interfaces;

use App\Models\Pornstar;
use Illuminate\Pagination\LengthAwarePaginator;

interface PornstarReadRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Pornstar;
    public function search(array $filter): LengthAwarePaginator;
}
