<?php

namespace App\Console\Commands;

use App\Jobs\FetchPornstarsJob;
use Illuminate\Console\Command;

class DispatchFetchPornstarsCommand extends Command
{
    protected $signature = 'pornstars:dispatch-fetch {--queue=fetching : Queue to dispatch the job to}';

    protected $description = 'Dispatch a job to fetch and process pornstar data in the background';

    public function handle()
    {
        $queue = $this->option('queue');

        $this->info('Dispatching pornstar fetch job to the "' . $queue . '" queue...');

        $job = new FetchPornstarsJob();
        $job->onQueue($queue);

        dispatch($job);

        $this->info('Job dispatched successfully!');
        $this->info('Run "php artisan queue:work --queue=' . $queue . '" to process the job.');

        return 0;
    }
}
