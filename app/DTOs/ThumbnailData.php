<?php

namespace App\DTOs;

class ThumbnailData
{
    public function __construct(
        public readonly array $urls,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?string $type = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            urls: isset($data['urls']) ? (array)$data['urls'] : (isset($data['url']) ? [$data['url']] : []),
            width: $data['width'] ?? null,
            height: $data['height'] ?? null,
            type: $data['type'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'urls' => $this->urls,
            'width' => $this->width,
            'height' => $this->height,
            'type' => $this->type
        ];
    }
}
