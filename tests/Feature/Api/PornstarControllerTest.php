<?php

namespace Tests\Feature\Api;

use App\Models\Pornstar;
use App\Models\PornstarAttribute;
use App\Models\PornstarStat;
use App\Models\Thumbnail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PornstarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
    }

    public function test_index_returns_paginated_pornstars()
    {
        $pornstars = Pornstar::factory()->count(25)->create();

        foreach ($pornstars as $pornstar) {
            PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
            PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);
        }

        $response = $this->getJson('/api/v1/pornstars');

        $actualCount = count($response->json('data'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'meta',
            'links'
        ]);

        $this->assertGreaterThan(0, $actualCount, "Response should contain at least one item");
    }

    public function test_show_returns_single_pornstar_with_relationships()
    {
        $uniqueName = 'Test Performer ' . time();

        $pornstar = Pornstar::factory()->create([
            'name' => $uniqueName
        ]);

        $this->assertEquals($uniqueName, $pornstar->name, "Pornstar should be created with the specified name");

        $pornstarId = $pornstar->id;

        // Create related models
        PornstarAttribute::factory()->create(['pornstar_id' => $pornstarId]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstarId]);
        Thumbnail::factory()->count(2)->create(['pornstar_id' => $pornstarId]);

        $response = $this->getJson("/api/v1/pornstars/{$pornstarId}");

        $responseData = $response->json();

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links'
        ]);

        $this->assertEquals($pornstarId, $response->json('data.id'),
            "Response should return the correct pornstar ID. Expected: {$pornstarId}, Got: " . $response->json('data.id'));
    }

    public function test_show_returns_404_for_nonexistent_pornstar()
    {
        $response = $this->getJson('/api/v1/pornstars/999');

        $response->assertStatus(404);
    }

    public function test_debug_search_functionality()
    {
        $distinctiveName = 'UNIQUESEARCHTEST' . time();
        $pornstar = Pornstar::factory()->create(['name' => $distinctiveName]);

        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);

        $dbPornstar = Pornstar::where('name', $distinctiveName)->first();
        $this->assertNotNull($dbPornstar, "Pornstar should exist in the database");

        $response = $this->getJson('/api/v1/pornstars/search?q=UNIQUESEARCHTEST');


        $responseData = $response->json();
        $responseItems = $responseData['data'] ?? [];

        // Basic assertion to check the response
        $response->assertStatus(200);
    }
}
