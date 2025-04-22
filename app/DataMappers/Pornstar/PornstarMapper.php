<?php

namespace App\DataMappers\Pornstar;

use App\Contracts\DataMappers\PornstarMapperInterface;
use App\Contracts\Services\Data\DataMapperInterface;
use App\DTOs\PornstarData;
use App\Models\Pornstar;
use App\Services\Pornstar\PornstarAttributeService;
use App\Services\Pornstar\PornstarAliasService;
use App\Services\Pornstar\PornstarRelationshipService;
use App\Services\Media\PornstarThumbnailService;
use Illuminate\Support\Facades\Log;

class PornstarMapper implements PornstarMapperInterface, DataMapperInterface
{
    public function __construct(
        private readonly PornstarAttributeService $attributeService,
        private readonly PornstarAliasService $aliasService,
        private readonly PornstarRelationshipService $relationshipService,
        private readonly PornstarThumbnailService $thumbnailService
    ) {}

    private function getCurrentDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }

    public function mapToModel(array $data): Pornstar
    {
        $pornstarData = PornstarData::fromArray($data);

        // Convert wlStatus to boolean if it's a string
        $wlStatus = is_string($pornstarData->wlStatus)
            ? $pornstarData->wlStatus === '1' || strtolower($pornstarData->wlStatus) === 'true'
            : (bool)$pornstarData->wlStatus;

        try {
            $pornstar = Pornstar::updateOrCreate(
                ['external_id' => (string)$pornstarData->externalId],
                [
                    'name' => (string)$pornstarData->name,
                    'license' => (string)$pornstarData->license,
                    'wl_status' => $wlStatus,
                    'link' => (string)$pornstarData->link,
                ]
            );

            if ($pornstarData->attributes !== null) {
                $this->attributeService->updateAttributes($pornstar, $pornstarData->attributes);
                $this->relationshipService->updateRelationships($pornstar, $pornstarData->attributes);
            }

            if (!empty($pornstarData->aliases)) {
                $this->aliasService->updateAliases($pornstar, $pornstarData->aliases);
            }

            if (!empty($pornstarData->thumbnails)) {
                $this->thumbnailService->updateThumbnails($pornstar, $pornstarData->thumbnails);
            }

            return $pornstar;

        } catch (\Exception $e) {
            Log::error('Error in pornstar mapping', [
                'name' => $pornstarData->name ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function mapFromModel(Pornstar $pornstar): array
    {
        return [
            'external_id' => $pornstar->external_id,
            'name' => $pornstar->name,
            'license' => $pornstar->license,
            'wl_status' => $pornstar->wl_status,
            'link' => $pornstar->link,
            'attributes' => $pornstar->attributes ? array_merge(
                $this->attributeService->mapFromModel($pornstar->attributes),
                [
                    'stats' => $pornstar->stats ? $this->attributeService->mapFromModel($pornstar->stats) : null,
                    'hair_colors' => $pornstar->hairColors->map(fn($color) => $this->relationshipService->mapFromModel($color))->all(),
                    'ethnicities' => $pornstar->ethnicities->map(fn($ethnicity) => $this->relationshipService->mapFromModel($ethnicity))->all()
                ]
            ) : null,
            'aliases' => $pornstar->aliases->map(fn($alias) => $this->aliasService->mapFromModel($alias))->all(),
            'thumbnails' => $pornstar->thumbnails->map(fn($thumbnail) => $this->thumbnailService->mapFromModel($thumbnail))->all()
        ];
    }

    public function mapManyToModel(array $dataArray): array
    {
        return array_map(fn($data) => $this->mapToModel($data), $dataArray);
    }

    public function mapManyFromModel(array $pornstars): array
    {
        return array_map(fn($pornstar) => $this->mapFromModel($pornstar), $pornstars);
    }

    public function map(array $item): array
    {
        $pornstar = $this->mapToModel($item);
        return $pornstar->toArray();
    }

    public function getAttributeProcessors(): array
    {
        return [];
    }

    public function registerProcessor(string $attributeName, $processor): DataMapperInterface
    {
        return $this;
    }

    public function getAttributeMapping(string $sourceAttribute): ?string
    {
        return null;
    }
}
