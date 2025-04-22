<?php

namespace App\DTOs;

class PornstarData
{
    public function __construct(
        public readonly int $externalId,
        public readonly string $name,
        public readonly ?string $license,
        public readonly bool $wlStatus,
        public readonly ?string $link,
        public readonly ?PornstarAttributeData $attributes = null,
        public readonly array $aliases = [],
        public readonly array $thumbnails = []
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = [];
        if (!empty($data['attributes'])) {
            $attributes = $data['attributes'];
            if (!empty($data['stats'])) {
                $attributes['stats'] = $data['stats'];
            }
        }

        $externalId = $data['external_id'] ?? $data['id'];
        if (!is_numeric($externalId)) {
            throw new \InvalidArgumentException('External ID must be numeric');
        }

        return new self(
            externalId: (int)$externalId,
            name: $data['name'] ?? '',
            license: $data['license'] ?? null,
            wlStatus: $data['wl_status'] ?? false,
            link: !empty($data['link']) && filter_var($data['link'], FILTER_VALIDATE_URL) ? $data['link'] : null,
            attributes: !empty($attributes) ? PornstarAttributeData::fromArray($attributes) : null,
            aliases: !empty($data['aliases']) ? array_map(fn($alias) => PornstarAliasData::fromArray($alias), $data['aliases']) : [],
            thumbnails: !empty($data['thumbnails']) ? array_map(fn($thumbnail) => ThumbnailData::fromArray($thumbnail), $data['thumbnails']) : []
        );
    }

    public function toArray(): array
    {
        return [
            'external_id' => $this->externalId,
            'name' => $this->name,
            'license' => $this->license,
            'wl_status' => $this->wlStatus,
            'link' => $this->link,
            'attributes' => $this->attributes?->toArray(),
            'aliases' => array_map(fn($alias) => $alias->toArray(), $this->aliases),
            'thumbnails' => array_map(fn($thumbnail) => $thumbnail->toArray(), $this->thumbnails)
        ];
    }
}
