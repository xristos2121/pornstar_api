<?php

namespace App\DTOs;

class PornstarStatData
{
    public function __construct(
        public readonly ?int $subscriptions = null,
        public readonly ?int $monthly_searches = null,
        public readonly ?int $views = null,
        public readonly ?int $videos_count = null,
        public readonly ?int $premium_videos_count = null,
        public readonly ?int $white_label_videos_count = null,
        public readonly ?int $rank = null,
        public readonly ?int $rank_premium = null,
        public readonly ?int $rank_wl = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            subscriptions: isset($data['subscriptions']) ? (int)$data['subscriptions'] : null,
            monthly_searches: isset($data['monthlySearches']) ? (int)$data['monthlySearches'] : null,
            views: isset($data['views']) ? (int)$data['views'] : null,
            videos_count: isset($data['videosCount']) ? (int)$data['videosCount'] : null,
            premium_videos_count: isset($data['premiumVideosCount']) ? (int)$data['premiumVideosCount'] : null,
            white_label_videos_count: isset($data['whiteLabelVideoCount']) ? (int)$data['whiteLabelVideoCount'] : null,
            rank: isset($data['rank']) ? (int)$data['rank'] : null,
            rank_premium: isset($data['rankPremium']) ? (int)$data['rankPremium'] : null,
            rank_wl: isset($data['rankWl']) ? (int)$data['rankWl'] : null
        );
    }

    public function toArray(): array
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
            'rank_wl' => $this->rank_wl
        ];
    }
}
