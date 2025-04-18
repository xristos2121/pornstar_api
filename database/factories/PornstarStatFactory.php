<?php

namespace Database\Factories;

use App\Models\PornstarStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PornstarStat>
 */
class PornstarStatFactory extends Factory
{
    protected $model = PornstarStat::class;

    public function definition(): array
    {
        return [
            'subscriptions' => fake()->numberBetween(0, 100000),
            'monthly_searches' => fake()->numberBetween(0, 500000),
            'views' => fake()->numberBetween(0, 1000000),
            'videos_count' => fake()->numberBetween(0, 1000),
            'premium_videos_count' => fake()->numberBetween(0, 500),
            'white_label_videos_count' => fake()->numberBetween(0, 200),
            'rank' => fake()->numberBetween(1, 1000),
            'rank_premium' => fake()->numberBetween(1, 1000),
            'rank_wl' => fake()->numberBetween(1, 1000)
        ];
    }
}
