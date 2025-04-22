<?php

namespace App\Services\Pornstar;

use App\Models\Pornstar;
use App\DTOs\PornstarAttributeData;
use App\Contracts\Repositories\PornstarAttributeRepositoryInterface;
use App\Contracts\Repositories\PornstarStatRepositoryInterface;
use App\Contracts\Services\Pornstar\PornstarAttributeServiceInterface;
use App\Contracts\Services\Util\DateTimeProviderInterface;

class PornstarAttributeService implements PornstarAttributeServiceInterface
{
    public function __construct(
        private readonly PornstarAttributeRepositoryInterface $attributeRepository,
        private readonly PornstarStatRepositoryInterface $statRepository,
        private readonly DateTimeProviderInterface $dateTimeProvider
    ) {}

    private function toBool($value): bool
    {
        if ($value === null) {
            return false;
        }
        
        if (is_string($value)) {
            return $value === '1' || strtolower($value) === 'true';
        }
        
        return (bool)$value;
    }

    public function updateAttributes(Pornstar $pornstar, PornstarAttributeData $data): void
    {
        $now = $this->dateTimeProvider->getCurrentDateTime();

        $attributeData = array_merge(
            $data->toArray(),
            [
                'pornstar_id' => $pornstar->id,
                'updated_at' => $now,
                'tattoos' => $this->toBool($data->tattoos),
                'piercings' => $this->toBool($data->piercings)
            ]
        );

        $this->attributeRepository->updateOrCreate(
            ['pornstar_id' => $pornstar->id],
            $attributeData
        );

        if ($data->stats !== null) {
            $statsData = array_merge(
                $data->stats->toArray(),
                [
                    'pornstar_id' => $pornstar->id,
                    'updated_at' => $now
                ]
            );

            $this->statRepository->updateOrCreate(
                ['pornstar_id' => $pornstar->id],
                $statsData
            );
        }
    }
}
