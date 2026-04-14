<?php

namespace App\Console\Commands;

use App\Jobs\RefreshAfricaHealthFactsJob;
use Illuminate\Console\Command;

class RefreshAfricaHealthFactsCommand extends Command
{
    protected $signature = 'facts:refresh-ai {--sync : Run immediately in this process (no queue worker)}';

    protected $description = 'Queue (or run) weekly refresh of “Did you know?” Africa health facts via OpenAI, with curated fallback if the API fails.';

    public function handle(): int
    {
        if ($this->option('sync')) {
            $this->info('Running facts refresh synchronously...');
            RefreshAfricaHealthFactsJob::dispatchSync();
            $this->info('Done.');
            return Command::SUCCESS;
        }

        RefreshAfricaHealthFactsJob::dispatch();
        $this->info('Africa health facts refresh job queued. Ensure a queue worker is running.');

        return Command::SUCCESS;
    }
}
