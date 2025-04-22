<?php

namespace App\Repositories;

use App\Contracts\Repositories\PornstarAliasRepositoryInterface;
use App\Models\PornstarAlias;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PornstarAliasRepository implements PornstarAliasRepositoryInterface
{
    public function __construct(
        private readonly PornstarAlias $aliasModel
    ) {}

    public function getByPornstarId(int $pornstarId): Collection
    {
        try {
            return $this->aliasModel->where('pornstar_id', $pornstarId)->get();
        } catch (\Exception $e) {
            Log::error('Failed to get pornstar aliases', [
                'pornstar_id' => $pornstarId,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    public function deleteAliases(int $pornstarId, array $aliasNames): void
    {
        try {
            $this->aliasModel->where('pornstar_id', $pornstarId)
                ->whereIn('alias', $aliasNames)
                ->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete pornstar aliases', [
                'pornstar_id' => $pornstarId,
                'aliases' => $aliasNames,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function createAliases(array $aliasRecords): void
    {
        try {
            DB::table($this->aliasModel->getTable())->insert($aliasRecords);
        } catch (\Exception $e) {
            Log::error('Failed to create pornstar aliases', [
                'records_count' => count($aliasRecords),
                'error' => $e->getMessage()
            ]);
        }
    }
}
