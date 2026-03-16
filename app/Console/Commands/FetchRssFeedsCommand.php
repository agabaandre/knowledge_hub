<?php

namespace App\Console\Commands;

use App\Models\RssFeed;
use App\Services\RssFetchService;
use Illuminate\Console\Command;

class FetchRssFeedsCommand extends Command
{
    protected $signature = 'rss:fetch {--feed= : Optional single feed ID}';

    protected $description = 'Fetch RSS feeds and create staging records for new items (run weekly).';

    public function handle(RssFetchService $fetchService): int
    {
        $feedId = $this->option('feed');
        $feeds = $feedId
            ? RssFeed::where('id', $feedId)->where('is_active', true)->get()
            : RssFeed::where('is_active', true)->get();

        if ($feeds->isEmpty()) {
            $this->info('No active feeds to fetch.');
            return Command::SUCCESS;
        }

        foreach ($feeds as $feed) {
            $this->info("Fetching: {$feed->name} ({$feed->url})");
            try {
                $result = $fetchService->fetchFeed($feed);
                $this->info("  Created: {$result['created']}, Skipped: {$result['skipped']}");
            } catch (\Throwable $e) {
                $this->error("  Error: " . $e->getMessage());
                $feed->update([
                    'last_fetched_at' => now(),
                    'last_fetch_status' => 'error',
                    'last_fetch_message' => $e->getMessage(),
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
