<?php

namespace Database\Factories;

use App\Models\PornstarAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PornstarAttribute>
 */
class PornstarAttributeFactory extends Factory
{
    protected $model = PornstarAttribute::class;

    public function definition(): array
    {
        return [
            'tattoos' => fake()->boolean(),
            'piercings' => fake()->boolean(),
            'breast_size' => fake()->boolean(),
            'breast_type' => fake()->boolean(),
            'orientation' => fake()->randomElement(['straight', 'gay', 'm2f']),
            'gender' => fake()->randomElement(['female', 'male']),
            'age' => fake()->numberBetween(18, 50)
        ];
    }
}
