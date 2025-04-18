<?php

namespace Database\Factories;

use App\Models\Ethnicity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ethnicity>
 */
class EthnicityFactory extends Factory
{
    protected $model = Ethnicity::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Asian',
                'Black',
                'Indian',
                'Latin',
                'Middle Eastern',
                'Mixed',
                'White'
            ])
        ];
    }
}
