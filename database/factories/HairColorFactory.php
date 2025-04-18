<?php

namespace Database\Factories;

use App\Models\HairColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HairColor>
 */
class HairColorFactory extends Factory
{
    protected $model = HairColor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'blonde',
                'brunette',
                'black',
                'red',
                'auburn',
                'brown',
                'dark brown',
                'light brown',
                'platinum blonde',
                'strawberry blonde',
                'ginger',
                'grey',
                'white',
                'multicolor'
            ])
        ];
    }
}
