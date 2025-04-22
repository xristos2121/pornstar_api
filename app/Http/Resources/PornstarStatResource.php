<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PornstarStatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subscriptions' => $this->subscriptions,
            'monthly_searches' => $this->monthly_searches,
            'views' => $this->views,
            'videos_count' => $this->videos_count,
            'premium_videos_count' => $this->premium_videos_count,
            'white_label_videos_count' => $this->white_label_videos_count,
            'rank' => $this->rank,
            'rank_premium' => $this->rank_premium,
            'rank_wl' => $this->rank_wl,
        ];
    }
}
