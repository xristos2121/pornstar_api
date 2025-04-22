<?php

namespace App\Console\Commands;

use App\Contracts\Services\Data\DataProcessorInterface;
use App\Contracts\Services\Data\ApiClientInterface;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\DB;

class FetchPornstarsCommand extends Command
{
    protected $signature = 'pornstars:fetch {--force : Force fetch even if data is recent}';
    protected $description = 'Fetch and update pornstar data from the external API';

    protected ApiClientInterface $dataFetcher;
    protected DataProcessorInterface $dataProcessor;

    public function __construct(
        ApiClientInterface $dataFetcher,
        DataProcessorInterface $dataProcessor
    ) {
        parent::__construct();
        $this->dataFetcher = $dataFetcher;
        $this->dataProcessor = $dataProcessor;
    }

    public function handle()
    {
        $this->info('Starting to fetch pornstar data...');

        if (!$this->dataFetcher->isSourceAvailable()) {
            $this->error('API source is not available. Please try again later.');
            return 1;
        }

        try {
            $this->info('Fetching data from API...');
            $data = $this->dataFetcher->fetch();

            $this->info('Fetched ' . count($data) . ' pornstars');
            $this->info('Processing pornstar data...');

            $processedCount = 0;
            $errors = [];

            // DB::beginTransaction();
            try {
                foreach ($data as $pornstarData) {
                    $name = $pornstarData['name'] ?? 'Unknown';

                    // Process using the data processor
                    $this->dataProcessor->process($data);
                    exit;
                    $processedCount++;

                    $this->info("✓ Successfully processed: {$name}");

                }
            } catch (\Exception $e) {
                // DB::rollBack();

                $error = "Failed to process: " . $e->getMessage();
                $this->error($error);
                $errors[] = $error;

                if ($this->option('verbose')) {
                    $this->error($e->getTraceAsString());
                    $this->info("Raw data for failed item:");
                    foreach ($data as $key => $value) {
                        if (is_array($value)) {
                            $this->info("  {$key}: " . json_encode($value));
                        } else {
                            $this->info("  {$key}: {$value}");
                        }
                    }
                }
            }

            // DB::commit();

            // Show summary
            $this->newLine();
            $this->info('=== Processing Summary ===');
            $this->info("Total pornstars: " . count($data));
            $this->info("Successfully processed: {$processedCount}");
            $this->info("Failed: " . (count($data) - $processedCount));

            if (!empty($errors)) {
                $this->newLine();
                $this->error('Errors encountered:');
                foreach ($errors as $error) {
                    $this->error("- {$error}");
                }
            }

            return count($data) === $processedCount ? 0 : 1;

        } catch (Exception $e) {
            $this->error('Fatal error: ' . $e->getMessage());
            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }
            return 1;
        }
    }
}
