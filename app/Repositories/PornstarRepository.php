<?php

namespace App\Repositories;

use App\Models\Pornstar;
use App\Repositories\Interfaces\PornstarReadRepositoryInterface;
use App\Repositories\Interfaces\PonstarWriteRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/*
 * @TODO: implement the interfaces
 */
class PornstarRepository
{
    public function __construct(private Pornstar $model) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage);
    }


}
