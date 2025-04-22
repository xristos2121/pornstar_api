<?php

namespace App\Services\Data;

use App\Contracts\Services\Data\DataMapperInterface;
use App\Contracts\Services\Data\DataProcessorInterface;
use App\DataMappers\Pornstar\PornstarMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PornstarDataProcessor implements DataProcessorInterface
{
    private DataMapperInterface $mapper;

    public function __construct(PornstarMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function process(array $data): array
    {
        if (!$this->validate($data)) {
            throw new \InvalidArgumentException('Invalid pornstar data format');
        }

        $items = $this->extractItems($data);
        $processed = [];

        foreach ($items['items'] as $item) {
            try {
                $item = $this->sanitizeData($item);
                $processed[] = $this->mapper->mapToModel($item);
                unset($item);
            } catch (\Exception $e) {
                Log::error("Error processing pornstar data: " . $e->getMessage(), [
                    'data' => $item,
                    'exception' => $e
                ]);
            }
        }

        unset($items);
        gc_collect_cycles();

        return $processed;
    }

    public function getDataMapper(): DataMapperInterface
    {
        return $this->mapper;
    }

    public function setDataMapper(DataMapperInterface $mapper): self
    {
        $this->mapper = $mapper;
        return $this;
    }

    public function validate(array $data): bool
    {
        if (!isset($data['data'])) {
            return false;
        }

        $items = $this->extractItems($data);

        if (empty($items)) {
            return false;
        }

        return true;
    }

    public function extractItems(array $data): array
    {
        return Arr::wrap(Arr::get($data, 'data', []));
    }

    private function sanitizeData(array $data): array
    {
        if (isset($data['id'])) {
            preg_match('/\d+/', $data['id'], $matches);
            $data['id'] = $matches[0] ?? null;
        }
        if (isset($data['external_id'])) {
            preg_match('/\d+/', $data['external_id'], $matches);
            $data['external_id'] = $matches[0] ?? null;
        }

        if (isset($data['wlStatus'])) {
            $data['wl_status'] = filter_var($data['wlStatus'], FILTER_VALIDATE_BOOLEAN);
        }

        return $data;
    }
}
