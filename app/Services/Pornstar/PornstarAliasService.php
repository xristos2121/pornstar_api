<?php

namespace App\Services\Pornstar;

use App\Contracts\Services\Pornstar\PornstarAliasServiceInterface;
use App\Contracts\Repositories\PornstarAliasRepositoryInterface;
use App\Contracts\Services\Util\DateTimeProviderInterface;
use App\Models\Pornstar;
use Illuminate\Support\Facades\Log;

class PornstarAliasService implements PornstarAliasServiceInterface
{
    public function __construct(
        private readonly PornstarAliasRepositoryInterface $aliasRepository,
        private readonly DateTimeProviderInterface $dateTimeProvider
    ) {}

    public function updateAliases(Pornstar $pornstar, array $aliases): void
    {
        if (empty($aliases)) {
            return;
        }

        $now = $this->dateTimeProvider->getCurrentDateTime();

        try {
            $newAliasNames = collect($aliases)
                ->map(fn($aliasData) => is_object($aliasData) ? (string)$aliasData->name : (string)$aliasData['name'])
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            $existingAliases = $this->aliasRepository->getByPornstarId($pornstar->id);
            $existingAliasNames = $existingAliases->pluck('alias')->toArray();

            $aliasesToAdd = array_diff($newAliasNames, $existingAliasNames);
            $aliasesToRemove = array_diff($existingAliasNames, $newAliasNames);

            if (!empty($aliasesToRemove)) {
                $this->aliasRepository->deleteAliases($pornstar->id, $aliasesToRemove);
            }

            if (!empty($aliasesToAdd)) {
                $aliasRecords = collect($aliasesToAdd)
                    ->map(fn($alias) => [
                        'alias' => $alias,
                        'pornstar_id' => $pornstar->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ])
                    ->toArray();

                $this->aliasRepository->createAliases($aliasRecords);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update pornstar aliases', [
                'pornstar_id' => $pornstar->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
