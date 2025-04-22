<?php

namespace App\Services\Pornstar;

use App\Contracts\Services\PornstarRelationshipServiceInterface;
use App\Contracts\Repositories\HairColorRepositoryInterface;
use App\Contracts\Repositories\EthnicityRepositoryInterface;
use App\Models\Pornstar;
use App\DTOs\PornstarAttributeData;
use App\Traits\HandlesTransactions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PornstarRelationshipService implements PornstarRelationshipServiceInterface
{
    use HandlesTransactions;

    public function __construct(
        private readonly HairColorRepositoryInterface $hairColorRepository,
        private readonly EthnicityRepositoryInterface $ethnicityRepository
    ) {}

    public function updateRelationships(Pornstar $pornstar, PornstarAttributeData $data): void
    {
        if (!empty($data->hair_colors)) {
            try {
                $this->updateHairColors($pornstar, $data->hair_colors);
            } catch (\Exception $e) {
                Log::error('Failed to update hair colors', [
                    'pornstar_id' => $pornstar->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if (!empty($data->ethnicities)) {
            try {
                $this->updateEthnicities($pornstar, $data->ethnicities);
            } catch (\Exception $e) {
                Log::error('Failed to update ethnicities', [
                    'pornstar_id' => $pornstar->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function updateHairColors(Pornstar $pornstar, array $hairColorAttributes): void
    {
        $hairColorNames = $this->extractAttributeNames($hairColorAttributes);

        if (empty($hairColorNames)) {
            return;
        }

        $hairColorIds = $this->hairColorRepository->findByNames($hairColorNames);

        if (empty($hairColorIds)) {
            foreach ($hairColorNames as $name) {
                $hairColor = $this->hairColorRepository->findOrCreateByName($name);
                if ($hairColor && !in_array($hairColor->id, $hairColorIds)) {
                    $hairColorIds[] = $hairColor->id;
                }
            }
        }

        if (!empty($hairColorIds)) {
            $this->hairColorRepository->syncModelAttributes($pornstar, 'hairColors', $hairColorIds);
        }
    }

    private function updateEthnicities(Pornstar $pornstar, array $ethnicityAttributes): void
    {
        $ethnicityNames = $this->extractAttributeNames($ethnicityAttributes);

        if (empty($ethnicityNames)) {
            return;
        }

        $records = $this->prepareAttributeRecords($ethnicityAttributes);
        $this->ethnicityRepository->insertOrIgnoreAttributes($records->toArray());

        $ethnicityIds = $this->ethnicityRepository->getIdsByNames($ethnicityNames);

        if (!empty($ethnicityIds)) {
            $this->ethnicityRepository->syncModelAttributes($pornstar, 'ethnicities', $ethnicityIds);
        }
    }

    private function extractAttributeNames(array $attributes): array
    {
        return collect($attributes)
            ->map(function ($attribute) {
                return is_object($attribute) ? $attribute->name : ($attribute['name'] ?? '');
            })
            ->unique()
            ->filter()
            ->values()
            ->toArray();
    }

    private function prepareAttributeRecords(array $attributes): Collection
    {
        return collect($attributes)
            ->map(function ($attribute) {
                $name = is_object($attribute) ? $attribute->name : ($attribute['name'] ?? '');
                return [
                    'name' => $name
                ];
            })
            ->unique('name')
            ->values();
    }
}
