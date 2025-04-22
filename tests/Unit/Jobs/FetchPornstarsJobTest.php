<?php

namespace Tests\Unit\Jobs;

use App\Contracts\Services\Data\ApiClientInterface;
use App\Contracts\Services\Data\DataProcessorInterface;
use App\Jobs\FetchPornstarsJob;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class FetchPornstarsJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_job_handles_api_unavailability()
    {
        $apiClient = Mockery::mock(ApiClientInterface::class);
        $apiClient->shouldReceive('isSourceAvailable')->once()->andReturn(false);

        $dataProcessor = Mockery::mock(DataProcessorInterface::class);
        $dataProcessor->shouldNotReceive('process');

        $job = new FetchPornstarsJob();
        $job->handle($apiClient, $dataProcessor);

        $apiClient->shouldHaveReceived('isSourceAvailable');
    }

    public function test_job_processes_data_successfully()
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

        $apiClient = Mockery::mock(ApiClientInterface::class);
        $apiClient->shouldReceive('isSourceAvailable')->once()->andReturn(true);
        $apiClient->shouldReceive('fetch')->once()->andReturn($sampleData);

        $dataProcessor = Mockery::mock(DataProcessorInterface::class);
        $dataProcessor->shouldReceive('process')
            ->once()
            ->with($sampleData)
            ->andReturn($processedResult);

        $job = new FetchPornstarsJob();
        $job->handle($apiClient, $dataProcessor);

        $apiClient->shouldHaveReceived('isSourceAvailable');
        $apiClient->shouldHaveReceived('fetch');
        $dataProcessor->shouldHaveReceived('process');
    }

    public function test_job_handles_processing_error()
    {
        $sampleData = [
            [
                'name' => 'Test Performer',
                'external_id' => '12345'
            ]
        ];

        $apiClient = Mockery::mock(ApiClientInterface::class);
        $apiClient->shouldReceive('isSourceAvailable')->once()->andReturn(true);
        $apiClient->shouldReceive('fetch')->once()->andReturn($sampleData);

        $dataProcessor = Mockery::mock(DataProcessorInterface::class);
        $dataProcessor->shouldReceive('process')
            ->once()
            ->with($sampleData)
            ->andThrow(new Exception('Processing error'));

        $job = new FetchPornstarsJob();
        
        try {
            $job->handle($apiClient, $dataProcessor);
            $this->fail('Expected exception was not thrown');
        } catch (Exception $e) {
            $this->assertEquals('Processing error', $e->getMessage());
        }

        $apiClient->shouldHaveReceived('isSourceAvailable');
        $apiClient->shouldHaveReceived('fetch');
        $dataProcessor->shouldHaveReceived('process');
    }

    public function test_job_processes_data_in_chunks()
    {
        $sampleData = [];
        for ($i = 1; $i <= 10; $i++) {
            $sampleData[] = [
                'name' => "Test Performer $i",
                'external_id' => "1234$i",
            ];
        }

        $chunk1Result = array_map(function ($item) {
            return ['id' => rand(1, 100), 'name' => $item['name'], 'processed' => true];
        }, array_slice($sampleData, 0, 5));

        $chunk2Result = array_map(function ($item) {
            return ['id' => rand(1, 100), 'name' => $item['name'], 'processed' => true];
        }, array_slice($sampleData, 5, 5));

        $apiClient = Mockery::mock(ApiClientInterface::class);
        $apiClient->shouldReceive('isSourceAvailable')->once()->andReturn(true);
        $apiClient->shouldReceive('fetch')->once()->andReturn($sampleData);

        $dataProcessor = Mockery::mock(DataProcessorInterface::class);

        $dataProcessor->shouldReceive('process')
            ->once()
            ->with(array_slice($sampleData, 0, 5))
            ->andReturn($chunk1Result);

        $dataProcessor->shouldReceive('process')
            ->once()
            ->with(array_slice($sampleData, 5, 5))
            ->andReturn($chunk2Result);

        $job = new FetchPornstarsJob(5);
        $job->handle($apiClient, $dataProcessor);

        $apiClient->shouldHaveReceived('isSourceAvailable');
        $apiClient->shouldHaveReceived('fetch');
        $dataProcessor->shouldHaveReceived('process')->twice();
    }
}
