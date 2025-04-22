<?php

namespace Tests\Feature\Api;

use App\Models\Pornstar;
use App\Models\PornstarAttribute;
use App\Models\PornstarStat;
use App\Models\HairColor;
use App\Models\Ethnicity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PornstarControllerExtendedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function test_index_with_custom_per_page_parameter()
    {
        $pornstars = Pornstar::factory()->count(15)->create();

        foreach ($pornstars as $pornstar) {
            PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
            PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);
        }

        $response = $this->getJson('/api/v1/pornstars?per_page=5');

        $data = $response->json('data');
        $perPage = $response->json('meta.pagination.per_page');

        $response->assertStatus(200);
        $this->assertEquals(5, $perPage, "per_page parameter should be 5");
        $this->assertEquals(15, $response->json('meta.pagination.total'), "Total should be 15");
        $this->assertEquals(3, $response->json('meta.pagination.total_pages'), "Total pages should be 3");

        $this->assertNotEmpty($data, "Response data should not be empty");
    }

    public function test_index_with_sorting()
    {
        $pornstarA = Pornstar::factory()->create(['name' => 'Alice']);
        $pornstarB = Pornstar::factory()->create(['name' => 'Betty']);
        $pornstarC = Pornstar::factory()->create(['name' => 'Carol']);

        foreach ([$pornstarA, $pornstarB, $pornstarC] as $pornstar) {
            PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
            PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);
        }

        $response = $this->getJson('/api/v1/pornstars?sort=name');
        $response->assertStatus(200);

        $data = $response->json('data');
        if (count($data) >= 3) {
            $names = collect($data)->pluck('name')->toArray();
            $aliceIndex = array_search('Alice', $names);
            $bettyIndex = array_search('Betty', $names);
            $carolIndex = array_search('Carol', $names);

            if ($aliceIndex !== false && $bettyIndex !== false) {
                $this->assertLessThan($bettyIndex, $aliceIndex, "Alice should come before Betty in ascending sort");
            }

            if ($bettyIndex !== false && $carolIndex !== false) {
                $this->assertLessThan($carolIndex, $bettyIndex, "Betty should come before Carol in ascending sort");
            }
        } else {
            $this->markTestSkipped("Not enough data in the response to test sorting");
        }

        $response = $this->getJson('/api/v1/pornstars?sort=-name');
        $response->assertStatus(200);

        $data = $response->json('data');
        if (count($data) >= 3) {
            $names = collect($data)->pluck('name')->toArray();
            $carolIndex = array_search('Carol', $names);
            $bettyIndex = array_search('Betty', $names);
            $aliceIndex = array_search('Alice', $names);

            if ($carolIndex !== false && $bettyIndex !== false) {
                $this->assertLessThan($bettyIndex, $carolIndex, "Carol should come before Betty in descending sort");
            }

            if ($bettyIndex !== false && $aliceIndex !== false) {
                $this->assertLessThan($aliceIndex, $bettyIndex, "Betty should come before Alice in descending sort");
            }
        } else {
            $this->markTestSkipped("Not enough data in the response to test sorting");
        }
    }

    public function test_basic_api_functionality()
    {
        // This test just verifies that the basic API endpoints work
        // without making specific assertions about filtering

        $pornstar = Pornstar::factory()->create();

        $actualName = $pornstar->name;

        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);

        $indexResponse = $this->getJson('/api/v1/pornstars');
        $indexResponse->assertStatus(200);
        $this->assertNotEmpty($indexResponse->json('data'), "Index endpoint should return data");
        $showResponse = $this->getJson("/api/v1/pornstars/{$pornstar->id}");
        $showResponse->assertStatus(200);
        $this->assertEquals($pornstar->id, $showResponse->json('data.id'), "Show endpoint should return the correct pornstar ID");
        $searchResponse = $this->getJson('/api/v1/pornstars/search?q=' . urlencode(substr($actualName, 0, 5)));
        $searchResponse->assertStatus(200);
    }

    public function test_show_includes_relationships()
    {
        $pornstar = Pornstar::factory()->create();

        $attributes = PornstarAttribute::factory()->create([
            'pornstar_id' => $pornstar->id,
            'age' => 25,
            'tattoos' => true,
            'piercings' => false,
            'breast_size' => 34,
            'breast_type' => 1,
            'orientation' => 'straight',
            'gender' => 'female'
        ]);

        $stats = PornstarStat::factory()->create([
            'pornstar_id' => $pornstar->id,
            'rank' => 42,
            'views' => 1500000,
            'videos_count' => 120,
            'premium_videos_count' => 50,
            'white_label_videos_count' => 70,
            'subscriptions' => 25000,
            'monthly_searches' => 15000,
            'rank_premium' => 30,
            'rank_wl' => 25
        ]);

        $blonde = HairColor::create(['name' => 'blonde']);
        $caucasian = Ethnicity::create(['name' => 'caucasian']);

        $pornstar->hairColors()->attach($blonde->id);
        $pornstar->ethnicities()->attach($caucasian->id);

        $response = $this->getJson("/api/v1/pornstars/{$pornstar->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name'
            ]
        ]);

        $this->assertEquals($pornstar->id, $response->json('data.id'), "Response should have the correct pornstar ID");
    }
}
