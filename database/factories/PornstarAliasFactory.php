<?php

namespace Database\Factories;

use App\Models\PornstarAlias;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PornstarAlias>
 */
class PornstarAliasFactory extends Factory
{
    protected $model = PornstarAlias::class;

    public function definition(): array
    {
        return [
            'alias' => fake()->name()
        ];
    }
}
