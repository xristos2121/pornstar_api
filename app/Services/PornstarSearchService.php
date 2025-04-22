<?php

namespace App\Services;

use App\Models\Pornstar;
use Illuminate\Database\Eloquent\Builder;

class PornstarSearchService
{
    public function search(?string $searchTerm, array $filter, string $sort, int $perPage, array $relations, bool $includeFacets): array
    {
        $query = Pornstar::with($relations);

        if (!empty($searchTerm)) {
            $terms = explode(' ', $searchTerm);

            $exactMatchIds = [];
            $exactMatch = Pornstar::where('name', $searchTerm)->first();
            if ($exactMatch) {
                $exactMatchIds[] = $exactMatch->id;
            }

            $aliasExactMatches = Pornstar::whereHas('aliases', function (Builder $q) use ($searchTerm) {
                $q->where('name', $searchTerm);
            })->get();

            foreach ($aliasExactMatches as $match) {
                $exactMatchIds[] = $match->id;
            }

            $query->where(function (Builder $q) use ($terms) {
                $q->where(function (Builder $nameQuery) use ($terms) {
                    foreach ($terms as $term) {
                        $nameQuery->orWhere('name', 'like', "%{$term}%");
                    }
                });

                $q->orWhereHas('aliases', function (Builder $aliasQuery) use ($terms) {
                    $aliasQuery->where(function (Builder $subQ) use ($terms) {
                        foreach ($terms as $term) {
                            $subQ->orWhere('name', 'like', "%{$term}%");
                        }
                    });
                });
            });

            if (!empty($exactMatchIds)) {
                $pornstarTable = (new Pornstar())->getTable();
                $query->orderByRaw("CASE WHEN {$pornstarTable}.id IN (" . implode(',', $exactMatchIds) . ") THEN 0 ELSE 1 END");
            }
        }

        if (!empty($filter)) {
            if (!empty($filter['hair_color'])) {
                $query->whereHas('hairColors', function (Builder $q) use ($filter) {
                    $q->where('name', $filter['hair_color']);
                });
            }

            if (!empty($filter['ethnicity'])) {
                $query->whereHas('ethnicities', function (Builder $q) use ($filter) {
                    $q->where('name', $filter['ethnicity']);
                });
            }

            // Apply age filter (format: "18..30" or "18..*" or "*..30")
            if (!empty($filter['age'])) {
                $this->applyRangeFilter($query, 'attributes', 'age', $filter['age']);
            }

            if (!empty($filter['height'])) {
                $this->applyRangeFilter($query, 'attributes', 'height', $filter['height']);
            }

            if (!empty($filter['weight'])) {
                $this->applyRangeFilter($query, 'attributes', 'weight', $filter['weight']);
            }

            if (!empty($filter['videos'])) {
                $this->applyRangeFilter($query, 'stats', 'videos', $filter['videos']);
            }

            if (!empty($filter['views'])) {
                $this->applyRangeFilter($query, 'stats', 'views', $filter['views']);
            }

            if (!empty($filter['rank'])) {
                $this->applyRangeFilter($query, 'stats', 'rank', $filter['rank']);
            }
        }

        $direction = 'asc';
        $field = $sort;


        if (strpos($sort, '-') === 0) {
            $direction = 'desc';
            $field = substr($sort, 1);
        }

        $pornstarModel = new Pornstar();
        $pornstarTable = $pornstarModel->getTable();

        switch ($field) {
            case 'age':
                 $attributesRelation = $pornstarModel->attributes();
                $attributesModel = $attributesRelation->getRelated();
                $attributesTable = $attributesModel->getTable();

                $query->join(
                    $attributesTable . ' as attrs',
                    $pornstarTable . '.id',
                    '=',
                    'attrs.pornstar_id'
                )
                ->orderBy('attrs.age', $direction)
                ->select($pornstarTable . '.*');
                break;

            case 'rank':
            case 'views':
            case 'videos':
                // Get the stats relationship and related model
                $statsRelation = $pornstarModel->stats();
                $statsModel = $statsRelation->getRelated();
                $statsTable = $statsModel->getTable();

                $query->join(
                    $statsTable . ' as stats',
                    $pornstarTable . '.id',
                    '=',
                    'stats.pornstar_id'
                )
                ->orderBy("stats.{$field}", $direction)
                ->select($pornstarTable . '.*');
                break;

            case 'relevance':
                break;

            default:
                $query->orderBy($pornstarTable . '.' . $field, $direction);
        }

        $pornstars = $query->paginate($perPage);

        return [
            'pornstars' => $pornstars
        ];
    }

    private function applyRangeFilter(Builder $query, string $relation, string $field, string $range): void
    {
        if (strpos($range, '..') !== false) {
            list($min, $max) = explode('..', $range);

            $query->whereHas($relation, function (Builder $q) use ($field, $min, $max) {
                if ($min !== '*') {
                    $q->where($field, '>=', $min);
                }

                if ($max !== '*') {
                    $q->where($field, '<=', $max);
                }
            });
        } else {
            $query->whereHas($relation, function (Builder $q) use ($field, $range) {
                $q->where($field, '=', $range);
            });
        }
    }
}
