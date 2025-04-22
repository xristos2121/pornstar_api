<?php

namespace Tests\Unit\Repositories;

use App\Models\HairColor;
use App\Models\Pornstar;
use App\Repositories\HairColorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HairColorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected HairColorRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new HairColorRepository();
    }

    public function test_insert_or_ignore_attributes_creates_new_records()
    {
        $attributes = [
            ['name' => 'blonde'],
            ['name' => 'brunette']
        ];

        $this->repository->insertOrIgnoreAttributes($attributes);

        $this->assertDatabaseHas('hair_colors', ['name' => 'blonde']);
        $this->assertDatabaseHas('hair_colors', ['name' => 'brunette']);
    }

    public function test_insert_or_ignore_attributes_handles_duplicates()
    {
        HairColor::create(['name' => 'blonde']);

        $attributes = [
            ['name' => 'blonde'], // Already exists
            ['name' => 'brunette'] // New
        ];

        $this->repository->insertOrIgnoreAttributes($attributes);

        $this->assertEquals(2, HairColor::count());
        $this->assertDatabaseHas('hair_colors', ['name' => 'blonde']);
        $this->assertDatabaseHas('hair_colors', ['name' => 'brunette']);
    }

    public function test_get_ids_by_names_returns_correct_ids()
    {
        $blonde = HairColor::create(['name' => 'blonde']);
        $brunette = HairColor::create(['name' => 'brunette']);

        $ids = $this->repository->getIdsByNames(['blonde', 'brunette']);

        $this->assertContains($blonde->id, $ids);
        $this->assertContains($brunette->id, $ids);
        $this->assertCount(2, $ids);
    }

    public function test_get_ids_by_names_creates_missing_records()
    {
        $blonde = HairColor::create(['name' => 'blonde']);

        $ids = $this->repository->getIdsByNames(['blonde', 'red']);

        $this->assertCount(2, $ids);
        $this->assertContains($blonde->id, $ids);
        $this->assertDatabaseHas('hair_colors', ['name' => 'red']);
    }

    public function test_sync_model_attributes_adds_relationships()
    {
        $pornstar = Pornstar::factory()->create();
        $blonde = HairColor::create(['name' => 'blonde']);
        $brunette = HairColor::create(['name' => 'brunette']);

        $this->repository->syncModelAttributes($pornstar, 'hairColors', [$blonde->id, $brunette->id]);

        $pornstar->refresh();

        $this->assertCount(2, $pornstar->hairColors);
        $this->assertTrue($pornstar->hairColors->contains($blonde->id));
        $this->assertTrue($pornstar->hairColors->contains($brunette->id));
    }

    public function test_sync_model_attributes_handles_empty_ids()
    {
        $pornstar = Pornstar::factory()->create();
        $this->repository->syncModelAttributes($pornstar, 'hairColors', []);
        $this->assertCount(0, $pornstar->hairColors);
    }

    public function test_find_or_create_by_name_returns_existing_record()
    {
        $blonde = HairColor::create(['name' => 'blonde']);
        $result = $this->repository->findOrCreateByName('blonde');
        $this->assertEquals($blonde->id, $result->id);
        $this->assertEquals('blonde', $result->name);
    }

    public function test_find_or_create_by_name_creates_new_record()
    {
        $result = $this->repository->findOrCreateByName('red');
        $this->assertNotNull($result);
        $this->assertEquals('red', $result->name);
        $this->assertDatabaseHas('hair_colors', ['name' => 'red']);
    }

    public function test_find_by_names_returns_correct_ids()
    {
        $blonde = HairColor::create(['name' => 'blonde']);
        $brunette = HairColor::create(['name' => 'brunette']);
        HairColor::create(['name' => 'red']);
        $ids = $this->repository->findByNames(['blonde', 'brunette']);
        $this->assertCount(2, $ids);
        $this->assertContains($blonde->id, $ids);
        $this->assertContains($brunette->id, $ids);
    }

    public function test_find_by_names_returns_empty_array_for_nonexistent_names()
    {
        $ids = $this->repository->findByNames(['nonexistent']);
        $this->assertIsArray($ids);
        $this->assertEmpty($ids);
    }
}
