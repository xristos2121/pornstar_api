<?php

namespace App\Repositories;

use App\Contracts\Repositories\HairColorRepositoryInterface;
use App\Models\HairColor;
use Illuminate\Support\Facades\Log;

class HairColorRepository implements HairColorRepositoryInterface
{
    public function insertOrIgnoreAttributes(array $attributes): void
    {
        $beforeCount = HairColor::count();
        Log::info('Hair colors table state before insert', [
            'count' => $beforeCount,
            'existing' => HairColor::all()->toArray()
        ]);


        foreach ($attributes as $attribute) {
            try {
                HairColor::updateOrCreate(
                    ['name' => $attribute['name']],
                    $attribute
                );
            } catch (\Exception $e) {
                Log::error('Failed to process hair color', [
                    'name' => $attribute['name'],
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function getIdsByNames(array $names): array
    {
        $hairColors = HairColor::whereIn('name', $names)->get();

        $existingNames = $hairColors->pluck('name')->toArray();
        $missingNames = array_diff($names, $existingNames);

        if (!empty($missingNames)) {
            foreach ($missingNames as $name) {
                try {
                    $newColor = HairColor::create(['name' => $name]);
                    $hairColors->push($newColor);
                } catch (\Exception $e) {
                    Log::error('Failed to create hair color', [
                        'name' => $name,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $ids = $hairColors->pluck('id')->toArray();

        return $ids;
    }

    public function syncModelAttributes(object $model, string $relation, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $existingColors = HairColor::whereIn('id', $ids)->get();
        $existingIds = $existingColors->pluck('id')->toArray();

        if (empty($existingIds)) {
            return;
        }

        try {
           $model->$relation()->sync($existingIds);

        } catch (\Exception $e) {
            Log::error('Failed to sync hair colors', [
                'error' => $e->getMessage(),
                'model_id' => $model->id,
                'relation' => $relation
            ]);
        }
    }
    
    public function findOrCreateByName(string $name): ?HairColor
    {
        try {
            return HairColor::firstOrCreate(['name' => $name]);
        } catch (\Exception $e) {
            Log::error('Failed to find or create hair color', [
                'name' => $name,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    public function findByNames(array $names): array
    {
        try {
            $hairColors = HairColor::whereIn('name', $names)->get();
            return $hairColors->pluck('id')->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to find hair colors by names', [
                'names' => $names,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
