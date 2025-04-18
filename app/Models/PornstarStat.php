<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PornstarStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'pornstar_id',
        'subscriptions',
        'monthly_searches',
        'views',
        'videos_count',
        'premium_videos_count',
        'white_label_videos_count',
        'rank',
        'rank_premium',
        'rank_wl'
    ];

    protected $casts = [
        'subscriptions' => 'integer',
        'monthly_searches' => 'integer',
        'views' => 'integer',
        'videos_count' => 'integer',
        'premium_videos_count' => 'integer',
        'white_label_videos_count' => 'integer',
        'rank' => 'integer',
        'rank_premium' => 'integer',
        'rank_wl' => 'integer'
    ];

    public function pornstar(): BelongsTo
    {
        return $this->belongsTo(Pornstar::class);
    }
}
