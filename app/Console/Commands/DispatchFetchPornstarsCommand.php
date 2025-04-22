<?php

namespace App\Console\Commands;

use App\Jobs\FetchPornstarsJob;
use Illuminate\Console\Command;

class DispatchFetchPornstarsCommand extends Command
{
    protected $signature = 'pornstars:dispatch-fetch 
                            {--queue=fetching : Queue to dispatch the job to}
                            {--chunk=100 : Chunk size for processing data}
                            {--force : Force update even if data exists}';

    protected $description = 'Dispatch a job to fetch and process pornstar data in the background';

    public function handle()
    {
        $queue = $this->option('queue');
        $chunkSize = (int) $this->option('chunk');
        $force = (bool) $this->option('force');

        $this->info('Dispatching pornstar fetch job to the "' . $queue . '" queue...');
        $this->info('Chunk size: ' . $chunkSize);
        
        if ($force) {
            $this->info('Force update enabled');
        }

        $job = new FetchPornstarsJob($chunkSize, $force);
        $job->onQueue($queue);

        dispatch($job);

        $this->info('Job dispatched successfully!');
        $this->info('Run "php artisan queue:work --queue=' . $queue . '" to process the job.');

        return 0;
    }
}
