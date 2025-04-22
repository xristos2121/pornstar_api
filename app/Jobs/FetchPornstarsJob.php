<?php

namespace App\Jobs;

use App\Contracts\Services\Data\ApiClientInterface;
use App\Contracts\Services\Data\DataProcessorInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class FetchPornstarsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 14400; // 4 hours

    public $failOnTimeout = true;

    protected int $chunkSize = 100;

    protected bool $force = false;

    public function __construct(int $chunkSize = 100, bool $force = false)
    {
        $this->onQueue('fetching');
        $this->chunkSize = $chunkSize;
        $this->force = $force;
    }

    public function handle(ApiClientInterface $dataFetcher, DataProcessorInterface $dataProcessor)
    {

        if (!$dataFetcher->isSourceAvailable()) {
            Log::error('API source is not available. Job failed.');
            $this->fail(new Exception('API source is not available'));
            return;
        }

        try {
            Log::info('Fetching data from API...');
            $data = $dataFetcher->fetch();

            Log::info('Fetched ' . count($data) . ' pornstars');
            Log::info('Processing pornstar data...');

            $processedCount = 0;
            $errors = [];

            try {
                if ($this->chunkSize > 0 && count($data) > $this->chunkSize) {
                    $chunks = array_chunk($data, $this->chunkSize);
                    Log::info('Processing data in ' . count($chunks) . ' chunks of ' . $this->chunkSize);

                    foreach ($chunks as $index => $chunk) {
                        Log::info('Processing chunk ' . ($index + 1) . ' of ' . count($chunks));
                        $result = $dataProcessor->process($chunk);
                        $processedCount += count($result);
                    }
                } else {
                    $result = $dataProcessor->process($data);
                    $processedCount = count($result);
                }

                Log::info('Successfully processed ' . $processedCount . ' pornstars');
            } catch (Exception $e) {
                $error = "Failed to process: " . $e->getMessage();
                Log::error($error);
                Log::error($e->getTraceAsString());
                $errors[] = $error;

                if (count($data) === 1) {
                    Log::info("Raw data for failed item:", $data[0]);
                }

                throw $e;
            }

            Log::info('=== Processing Summary ===');
            Log::info("Total pornstars: " . count($data));
            Log::info("Successfully processed: {$processedCount}");
            Log::info("Failed: " . (count($data) - $processedCount));

            if (!empty($errors)) {
                Log::error('Errors encountered:', $errors);
            }

            Log::info('Pornstar data fetch job completed successfully');
        } catch (Exception $e) {
            Log::error('Fatal error in pornstar fetch job: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception)
    {
        Log::error('Pornstar fetch job failed: ' . $exception->getMessage(), [
            'exception' => $exception
        ]);
    }
}
