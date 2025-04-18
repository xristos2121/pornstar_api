<?php

namespace Database\Factories;

use App\Models\Thumbnail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thumbnail>
 */
class ThumbnailFactory extends Factory
{
    protected $model = Thumbnail::class;

    public function definition(): array
    {
        return [
            'height' => fake()->randomElement([240, 480, 720, 1080]),
            'width' => fake()->randomElement([320, 640, 1280, 1920]),
            'type' => fake()->randomElement(['pc', 'mobile', 'tablet'])
        ];
    }
}
