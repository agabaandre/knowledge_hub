<?php

namespace App\Jobs;

use App\Models\RssFeed;
use App\Services\RssFetchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchRssFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const CACHE_PREFIX = 'rss_fetch_run_';
    public const CACHE_TTL = 600; // 10 minutes

    public function __construct(
        public ?string $runId = null,
        public ?int $feedId = null
    ) {
        $this->onQueue('default');
    }

    public function handle(RssFetchService $fetchService): void
    {
        $key = $this->runId ? self::CACHE_PREFIX . $this->runId : null;
        if ($key) {
            $this->setProgress($key, 'running', 0, 'Starting fetch...');
        }

        $feeds = $this->feedId
            ? RssFeed::where('id', $this->feedId)->where('is_active', true)->get()
            : RssFeed::where('is_active', true)->orderBy('name')->get();

        if ($feeds->isEmpty()) {
            if ($key) {
                $this->setProgress($key, 'completed', 100, 'No active feeds to fetch.', 0, 0);
            }
            return;
        }

        $total = $feeds->count();
        $totalCreated = 0;
        $totalSkipped = 0;

        foreach ($feeds as $i => $feed) {
            $pct = (int) round((($i + 1) / $total) * 100);
            if ($key) {
                $this->setProgress($key, 'running', $pct, "Fetching: {$feed->name}...");
            }

            try {
                $result = $fetchService->fetchFeed($feed);
                $totalCreated += $result['created'];
                $totalSkipped += $result['skipped'];
            } catch (\Throwable $e) {
                Log::warning('RSS fetch job error: ' . $e->getMessage(), ['feed_id' => $feed->id]);
                $feed->update([
                    'last_fetched_at' => now(),
                    'last_fetch_status' => 'error',
                    'last_fetch_message' => $e->getMessage(),
                ]);
            }
        }

        $message = "Done. Created: {$totalCreated}, Skipped: {$totalSkipped}.";
        if ($key) {
            $this->setProgress($key, 'completed', 100, $message, $totalCreated, $totalSkipped);
        }
    }

    public function failed(\Throwable $e): void
    {
        if ($this->runId) {
            $key = self::CACHE_PREFIX . $this->runId;
            $this->setProgress($key, 'error', 0, 'Fetch failed: ' . $e->getMessage(), 0, 0);
        }
    }

    private function setProgress(
        string $key,
        string $status,
        int $progress,
        string $message,
        ?int $created = null,
        ?int $skipped = null
    ): void {
        $data = [
            'status' => $status,
            'progress' => $progress,
            'message' => $message,
            'created' => $created,
            'skipped' => $skipped,
            'updated_at' => now()->toIso8601String(),
        ];
        Cache::put($key, $data, self::CACHE_TTL);
    }
}
