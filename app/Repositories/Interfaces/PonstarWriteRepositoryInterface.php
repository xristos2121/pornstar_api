<?php

namespace App\Repositories\Interfaces;

use App\Models\Pornstar;

interface PonstarWriteRepositoryInterface
{
    public function create(array $data): Pornstar;
    public function update(int $id, array $data): Pornstar;
    public function delete(int $id): void;
}
