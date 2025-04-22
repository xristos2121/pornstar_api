<?php

namespace Tests\Unit\Services;

use App\Models\Pornstar;
use App\Models\PornstarAlias;
use App\Models\PornstarAttribute;
use App\Models\PornstarStat;
use App\Models\HairColor;
use App\Models\Ethnicity;
use App\Services\PornstarSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PornstarSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PornstarSearchService $searchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchService = new PornstarSearchService();
    }

    public function test_search_by_name_returns_matching_pornstars()
    {
        $pornstar1 = Pornstar::factory()->create(['name' => 'Jane Doe']);
        $pornstar2 = Pornstar::factory()->create(['name' => 'John Smith']);

        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar2->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar2->id]);

        $result = $this->searchService->search(
            'Jane', // search term
            [], // no filters
            'name', // sort by name
            10, // per page
            [], // no relations
            false // no facets
        );

        $this->assertCount(1, $result['pornstars']);
        $this->assertEquals($pornstar1->id, $result['pornstars']->first()->id);
    }

    public function test_search_with_age_filter_returns_filtered_results()
    {
        $pornstar1 = Pornstar::factory()->create(['name' => 'Performer 1']);
        $pornstar2 = Pornstar::factory()->create(['name' => 'Performer 2']);

        PornstarAttribute::factory()->create([
            'pornstar_id' => $pornstar1->id,
            'age' => 25
        ]);

        PornstarAttribute::factory()->create([
            'pornstar_id' => $pornstar2->id,
            'age' => 35
        ]);

        PornstarStat::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar2->id]);

        $result = $this->searchService->search(
            null, // no search term
            ['age' => '20..30'], // age filter
            'name', // sort by name
            10, // per page
            [], // no relations
            false // no facets
        );

        $this->assertCount(1, $result['pornstars']);
        $this->assertEquals($pornstar1->id, $result['pornstars']->first()->id);
    }

    public function test_search_with_hair_color_filter()
    {
        $pornstar1 = Pornstar::factory()->create(['name' => 'Performer 1']);
        $pornstar2 = Pornstar::factory()->create(['name' => 'Performer 2']);

        $blonde = HairColor::create(['name' => 'blonde']);
        $brunette = HairColor::create(['name' => 'brunette']);

        $pornstar1->hairColors()->attach($blonde->id);
        $pornstar2->hairColors()->attach($brunette->id);

        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar2->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar2->id]);

        $result = $this->searchService->search(
            null, // no search term
            ['hair_color' => 'blonde'], // hair color filter
            'name', // sort by name
            10, // per page
            [], // no relations
            false // no facets
        );

        $this->assertCount(1, $result['pornstars']);
        $this->assertEquals($pornstar1->id, $result['pornstars']->first()->id);
    }

    public function test_search_with_ethnicity_filter()
    {
        $pornstar1 = Pornstar::factory()->create(['name' => 'Performer 1']);
        $pornstar2 = Pornstar::factory()->create(['name' => 'Performer 2']);

        $caucasian = Ethnicity::create(['name' => 'caucasian']);
        $asian = Ethnicity::create(['name' => 'asian']);

        $pornstar1->ethnicities()->attach($caucasian->id);
        $pornstar2->ethnicities()->attach($asian->id);

        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar2->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar2->id]);

        $result = $this->searchService->search(
            null, // no search term
            ['ethnicity' => 'asian'], // ethnicity filter
            'name', // sort by name
            10, // per page
            [], // no relations
            false // no facets
        );

        $this->assertCount(1, $result['pornstars']);
        $this->assertEquals($pornstar2->id, $result['pornstars']->first()->id);
    }

    public function test_search_with_alias_matching()
    {
        $pornstar1 = Pornstar::factory()->create(['name' => 'Primary Name']);
        $pornstar2 = Pornstar::factory()->create(['name' => 'Other Performer']);

        PornstarAlias::create([
            'pornstar_id' => $pornstar1->id,
            'alias' => 'Alternate Name'
        ]);

        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarAttribute::factory()->create(['pornstar_id' => $pornstar2->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar1->id]);
        PornstarStat::factory()->create(['pornstar_id' => $pornstar2->id]);

        $result = $this->searchService->search(
            'Alternate', // search term matching alias
            [], // no filters
            'name', // sort by name
            10, // per page
            [], // no relations
            false // no facets
        );

        $this->assertCount(1, $result['pornstars']);
        $this->assertEquals($pornstar1->id, $result['pornstars']->first()->id);
    }

    public function test_search_with_sorting_by_name()
    {
        $pornstarA = Pornstar::factory()->create(['name' => 'Alice']);
        $pornstarB = Pornstar::factory()->create(['name' => 'Betty']);
        $pornstarC = Pornstar::factory()->create(['name' => 'Carol']);

        foreach ([$pornstarA, $pornstarB, $pornstarC] as $pornstar) {
            PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
            PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);
        }

        $result = $this->searchService->search(
            null, // no search term
            [], // no filters
            'name', // sort by name ascending
            10, // per page
            [], // no relations
            false // no facets
        );

        $pornstars = $result['pornstars'];
        $this->assertEquals('Alice', $pornstars[0]->name);
        $this->assertEquals('Betty', $pornstars[1]->name);
        $this->assertEquals('Carol', $pornstars[2]->name);

        $result = $this->searchService->search(
            null, // no search term
            [], // no filters
            '-name', // sort by name descending
            10, // per page
            [], // no relations
            false // no facets
        );

        $pornstars = $result['pornstars'];
        $this->assertEquals('Carol', $pornstars[0]->name);
        $this->assertEquals('Betty', $pornstars[1]->name);
        $this->assertEquals('Alice', $pornstars[2]->name);
    }

    public function test_search_with_pagination()
    {
        $pornstars = Pornstar::factory()->count(15)->create();

        foreach ($pornstars as $pornstar) {
            PornstarAttribute::factory()->create(['pornstar_id' => $pornstar->id]);
            PornstarStat::factory()->create(['pornstar_id' => $pornstar->id]);
        }

        $result = $this->searchService->search(
            null, // no search term
            [], // no filters
            'name', // sort by name
            5, // per page
            [], // no relations
            false // no facets
        );

        $this->assertEquals(5, $result['pornstars']->count());
        $this->assertEquals(15, $result['pornstars']->total());
        $this->assertEquals(5, $result['pornstars']->perPage());
        $this->assertEquals(1, $result['pornstars']->currentPage());
        $this->assertEquals(3, $result['pornstars']->lastPage());
    }
}
