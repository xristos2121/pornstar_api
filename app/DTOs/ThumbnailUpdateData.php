<?php

namespace App\DTOs;

class ThumbnailUpdateData
{
    public function __construct(
        public readonly string $type,
        public readonly array $urls,
        public readonly ?int $width = null,
        public readonly ?int $height = null
    ) {}

    public static function fromObject(object $data): self
    {
        return new self(
            type: $data->type,
            urls: is_array($data->urls) ? $data->urls : [$data->urls],
            width: $data->width ?? null,
            height: $data->height ?? null
        );
    }
}
