<?php

namespace App\DataMappers\Pornstar;

use App\Contracts\DataMappers\PornstarStatMapperInterface;
use App\Models\PornstarStat;

class PornstarStatMapper implements PornstarStatMapperInterface
{
    public function mapToModel(array $data): PornstarStat
    {
        $attributes = [
            'pornstar_id' => $data['pornstar_id'],
            'subscriptions' => $data['subscriptions'] ?? 0,
            'monthly_searches' => $data['monthly_searches'] ?? 0,
            'views' => $data['views'] ?? 0,
            'videos_count' => $data['videos_count'] ?? 0,
            'premium_videos_count' => $data['premium_videos_count'] ?? 0,
            'white_label_videos_count' => $data['white_label_videos_count'] ?? 0,
            'rank' => $data['rank'] ?? null,
            'rank_premium' => $data['rank_premium'] ?? null,
            'rank_wl' => $data['rank_wl'] ?? null,
        ];

        if (isset($data['created_at'])) {
            $attributes['created_at'] = $data['created_at'];
        }
        if (isset($data['updated_at'])) {
            $attributes['updated_at'] = $data['updated_at'];
        }

        return PornstarStat::create($attributes);
    }
}
