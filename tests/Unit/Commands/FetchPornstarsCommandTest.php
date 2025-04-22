<?php

namespace Tests\Unit\Commands;

use App\Contracts\Services\Data\ApiClientInterface;
use App\Contracts\Services\Data\DataProcessorInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class FetchPornstarsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_fetch_pornstars_command_handles_api_unavailability()
    {
        $this->mock(ApiClientInterface::class, function ($mock) {
            $mock->shouldReceive('isSourceAvailable')->once()->andReturn(false);
        });

        $this->artisan('pornstars:fetch')
             ->expectsOutput('Starting to fetch pornstar data...')
             ->expectsOutput('API source is not available. Please try again later.')
             ->assertExitCode(1);
    }

    public function test_fetch_pornstars_command_processes_data_successfully()
    {
        $sampleData = [
            [
                'name' => 'Test Performer',
                'external_id' => '12345',
                'hair_colors' => ['blonde'],
                'ethnicities' => ['caucasian']
            ]
        ];

        $processedResult = [
            [
                'id' => 1,
                'name' => 'Test Performer',
                'external_id' => '12345',
                'processed' => true
            ]
        ];

        $this->mock(ApiClientInterface::class, function ($mock) use ($sampleData) {
            $mock->shouldReceive('isSourceAvailable')->once()->andReturn(true);
            $mock->shouldReceive('fetch')->once()->andReturn($sampleData);
        });

        $this->mock(DataProcessorInterface::class, function ($mock) use ($sampleData, $processedResult) {
            $mock->shouldReceive('process')->once()->with($sampleData)->andReturn($processedResult);
        });

        $this->artisan('pornstars:fetch')
             ->expectsOutput('Starting to fetch pornstar data...')
             ->expectsOutput('Fetching data from API...')
             ->expectsOutput('Fetched 1 pornstars')
             ->expectsOutput('Processing pornstar data...')
             ->expectsOutput('✓ Successfully processed: Test Performer')
             ->assertExitCode(0);
    }

    public function test_fetch_pornstars_command_handles_processing_error()
    {
        $sampleData = [
            [
                'name' => 'Test Performer',
                'external_id' => '12345'
            ]
        ];

        $this->mock(ApiClientInterface::class, function ($mock) use ($sampleData) {
            $mock->shouldReceive('isSourceAvailable')->once()->andReturn(true);
            $mock->shouldReceive('fetch')->once()->andReturn($sampleData);
        });

        $this->mock(DataProcessorInterface::class, function ($mock) use ($sampleData) {
            $mock->shouldReceive('process')
                 ->once()
                 ->with($sampleData)
                 ->andThrow(new \Exception('Processing error'));
        });

        $this->artisan('pornstars:fetch')
             ->expectsOutput('Starting to fetch pornstar data...')
             ->expectsOutput('Fetching data from API...')
             ->expectsOutput('Fetched 1 pornstars')
             ->expectsOutput('Processing pornstar data...')
             ->expectsOutput('Failed to process: Processing error')
             ->assertExitCode(1);
    }
}
