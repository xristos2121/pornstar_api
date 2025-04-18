<?php

namespace Database\Factories;

use App\Models\ThumbnailUrl;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThumbnailUrl>
 */
class ThumbnailUrlFactory extends Factory
{
    protected $model = ThumbnailUrl::class;

    public function definition(): array
    {
        return [
            'url' => fake()->imageUrl(),
            'cached_path' => fake()->optional()->filePath()
        ];
    }
}
