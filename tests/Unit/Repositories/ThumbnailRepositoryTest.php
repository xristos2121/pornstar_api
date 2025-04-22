<?php

namespace Tests\Unit\Repositories;

use App\Models\Pornstar;
use App\Models\Thumbnail;
use App\Models\ThumbnailUrl;
use App\Repositories\ThumbnailRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThumbnailRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ThumbnailRepository(
            new ThumbnailUrl(),
            new Thumbnail()
        );
    }

    public function test_create_thumbnail()
    {
        $pornstar = Pornstar::factory()->create();

        $type = 'pc';

        $thumbnail = $this->repository->createThumbnail([
            'pornstar_id' => $pornstar->id,
            'type' => $type,
            'height' => 300,
            'width' => 400
        ]);

        $this->assertInstanceOf(Thumbnail::class, $thumbnail);
        $this->assertEquals($type, $thumbnail->type);
        $this->assertEquals($pornstar->id, $thumbnail->pornstar_id);

        $this->assertDatabaseHas('thumbnails', [
            'pornstar_id' => $pornstar->id,
            'type' => $type
        ]);
    }

    public function test_create_thumbnail_url()
    {
        $pornstar = Pornstar::factory()->create();
        $type = 'mobile';
        $url = 'https://example.com/image.jpg';

        $thumbnail = $this->repository->createThumbnail([
            'pornstar_id' => $pornstar->id,
            'type' => $type,
            'height' => 200,
            'width' => 300
        ]);

        $thumbnailUrl = $this->repository->createThumbnailUrl($thumbnail, $url);

        $this->assertInstanceOf(ThumbnailUrl::class, $thumbnailUrl);
        $this->assertEquals($url, $thumbnailUrl->url);
        $this->assertEquals($thumbnail->id, $thumbnailUrl->thumbnail_id);

        $this->assertDatabaseHas('thumbnail_urls', [
            'thumbnail_id' => $thumbnail->id,
            'url' => $url
        ]);
    }

    public function test_find_existing_url()
    {
        $pornstar = Pornstar::factory()->create();
        $type = 'tablet';
        $url = 'https://example.com/image.jpg';

        $thumbnail = Thumbnail::create([
            'pornstar_id' => $pornstar->id,
            'type' => $type,
            'height' => 500,
            'width' => 800
        ]);

        ThumbnailUrl::create([
            'thumbnail_id' => $thumbnail->id,
            'url' => $url
        ]);

        $foundUrl = $this->repository->findExistingUrl($pornstar->id, $type, $url);

        $this->assertNotNull($foundUrl);
        $this->assertEquals($url, $foundUrl->url);
    }

    public function test_find_thumbnail_by_type()
    {
        $pornstar = Pornstar::factory()->create();

        Thumbnail::create([
            'pornstar_id' => $pornstar->id,
            'type' => 'pc',
            'height' => 600,
            'width' => 800
        ]);

        Thumbnail::create([
            'pornstar_id' => $pornstar->id,
            'type' => 'mobile',
            'height' => 300,
            'width' => 400
        ]);

        $pcThumbnail = $this->repository->findThumbnailByType($pornstar->id, 'pc');

        $this->assertNotNull($pcThumbnail);
        $this->assertEquals('pc', $pcThumbnail->type);
        $this->assertEquals($pornstar->id, $pcThumbnail->pornstar_id);

        $mobileThumbnail = $this->repository->findThumbnailByType($pornstar->id, 'mobile');

        $this->assertNotNull($mobileThumbnail);
        $this->assertEquals('mobile', $mobileThumbnail->type);
    }

    public function test_update_thumbnail()
    {
        $pornstar = Pornstar::factory()->create();

        $thumbnail = Thumbnail::create([
            'pornstar_id' => $pornstar->id,
            'type' => 'pc',
            'height' => 600,
            'width' => 800,
            'active' => true
        ]);

        $updated = $this->repository->updateThumbnail($thumbnail, [
            'height' => 700,
            'width' => 900
        ]);

        $this->assertTrue($updated);

        $thumbnail->refresh();

        $this->assertEquals(700, $thumbnail->height);
        $this->assertEquals(900, $thumbnail->width);
    }
}
