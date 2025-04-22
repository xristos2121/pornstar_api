<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FetchPornstarsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 14400; // 4 hours

    public $failOnTimeout = true;

    public function __construct()
    {
        $this->onQueue('fetching');
    }

    public function handle()
    {
        Log::info('Starting pornstar data fetch job');

        try {
            $exitCode = Artisan::call('pornstars:fetch', [
                '--chunk' => 100,
                '--verbose' => true
            ]);

            $output = Artisan::output();
            Log::info('Pornstar fetch command output:', ['output' => $output]);

            if ($exitCode !== 0) {
                Log::error('Pornstar fetch command failed with exit code: ' . $exitCode);
                throw new \Exception('Command failed with exit code: ' . $exitCode);
            }

            Log::info('Pornstar data fetch job completed successfully');
        } catch (\Exception $e) {
            Log::error('Error in pornstar fetch job: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            throw $e;
        }
    }
}
