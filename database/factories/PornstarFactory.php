<?php

namespace Database\Factories;

use App\Models\Pornstar;
use App\Models\HairColor;
use App\Models\Ethnicity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pornstar>
 */
class PornstarFactory extends Factory
{
    protected $model = Pornstar::class;

    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->numberBetween(1, 100000),
            'name' => fake()->name(),
            'license' => fake()->optional()->word(),
            'wl_status' => fake()->boolean(),
            'link' => fake()->url()
        ];
    }

    public function withHairColors(int $count = 1): static
    {
        return $this->afterCreating(function (Pornstar $pornstar) use ($count) {
            $hairColors = HairColor::inRandomOrder()->limit($count)->get();
            $pornstar->hairColors()->attach($hairColors);
        });
    }

    public function withEthnicities(int $count = 1): static
    {
        return $this->afterCreating(function (Pornstar $pornstar) use ($count) {
            $ethnicities = Ethnicity::inRandomOrder()->limit($count)->get();
            $pornstar->ethnicities()->attach($ethnicities);
        });
    }

    public function withAttributes(): static
    {
        return $this->has(
            PornstarAttributeFactory::new(),
            'attributes'
        );
    }

    public function withStats(): static
    {
        return $this->has(
            PornstarStatFactory::new(),
            'stats'
        );
    }

    public function withAliases(int $count = 1): static
    {
        return $this->has(
            PornstarAliasFactory::new()->count($count),
            'aliases'
        );
    }

    public function withThumbnails(int $count = 1, int $urlsPerThumbnail = 1): static
    {
        return $this->has(
            ThumbnailFactory::new()
                ->count($count)
                ->has(
                    ThumbnailUrlFactory::new()->count($urlsPerThumbnail),
                    'urls'
                ),
            'thumbnails'
        );
    }

    public function complete(): static
    {
        return $this->withAttributes()
            ->withStats()
            ->withHairColors(2)
            ->withEthnicities(2)
            ->withAliases(3)
            ->withThumbnails(3, 2);
    }
}
