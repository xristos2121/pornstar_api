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

class ProcessPornstarsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ApiClientInterface $dataFetcher;
    protected DataProcessorInterface $dataProcessor;
    protected array $data;
    protected bool $force;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, bool $force)
    {
        $this->data = $data;
        $this->force = $force;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting ProcessPornstarsJob', [
                'data_count' => count($this->data),
                'force' => $this->force
            ]);

            $this->dataProcessor = app(DataProcessorInterface::class);
            
            Log::info('Processing pornstar data...');
            $processedCount = 0;
            
            foreach ($this->data as $pornstarData) {
                try {
                    Log::info('Processing pornstar', [
                        'name' => $pornstarData['name'] ?? 'Unknown',
                        'id' => $pornstarData['id'] ?? 'No ID'
                    ]);
                    
                    $this->dataProcessor->process([$pornstarData]);
                    $processedCount++;
                    
                    Log::info('Successfully processed pornstar', [
                        'name' => $pornstarData['name'] ?? 'Unknown',
                        'processed_count' => $processedCount,
                        'total_count' => count($this->data)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error processing individual pornstar', [
                        'name' => $pornstarData['name'] ?? 'Unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            Log::info('Pornstars data processing completed', [
                'total_processed' => $processedCount,
                'total_failed' => count($this->data) - $processedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ProcessPornstarsJob', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
