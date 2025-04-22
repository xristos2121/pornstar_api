<?php

namespace App\Repositories;

use App\Contracts\Repositories\EthnicityRepositoryInterface;
use App\Models\Ethnicity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EthnicityRepository implements EthnicityRepositoryInterface
{
    public function insertOrIgnoreAttributes(array $attributes): void
    {
        try {
            DB::transaction(function () use ($attributes) {
                foreach ($attributes as $attribute) {
                    Ethnicity::firstOrCreate(
                        ['name' => $attribute['name']],
                        $attribute
                    );
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to insert ethnicity attributes', [
                'error' => $e->getMessage(),
                'attributes' => $attributes
            ]);
        }
    }

    public function getIdsByNames(array $names): array
    {
        return Ethnicity::whereIn('name', $names)
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function syncModelAttributes(object $model, string $relation, array $ids): void
    {
        try {
            $existingIds = Ethnicity::whereIn('id', $ids)->pluck('id')->toArray();
            $model->$relation()->sync($existingIds);
        } catch (\Exception $e) {
            Log::error('Failed to sync ethnicities', [
                'error' => $e->getMessage(),
                'model_id' => $model->id,
                'relation' => $relation
            ]);
        }
    }
    
    public function findOrCreateByName(string $name): ?Ethnicity
    {
        try {
            return Ethnicity::firstOrCreate(['name' => $name]);
        } catch (\Exception $e) {
            Log::error('Failed to find or create ethnicity', [
                'name' => $name,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    public function findByNames(array $names): array
    {
        try {
            $ethnicities = Ethnicity::whereIn('name', $names)->get();
            return $ethnicities->pluck('id')->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to find ethnicities by names', [
                'names' => $names,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
