<?php

namespace App\Jobs;

use App\Models\Fact;
use App\Services\ChatGPTService;
use App\Support\AfricaHealthFactsFallback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshAfricaHealthFactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 360;

    public int $tries = 2;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(ChatGPTService $chat): void
    {
        $source = 'openai';
        $rows = null;

        $result = $chat->generateAfricaHealthFacts(24);
        if (($result['ok'] ?? false) && ! empty($result['facts'])) {
            $rows = $result['facts'];
        } else {
            Log::warning('RefreshAfricaHealthFactsJob: OpenAI unavailable or invalid response; using curated fallback.', [
                'error' => $result['error'] ?? 'unknown',
            ]);
            $rows = AfricaHealthFactsFallback::facts();
            $source = 'fallback';
        }

        DB::transaction(function () use ($rows): void {
            Fact::query()->where('is_ai_managed', true)->delete();
            foreach ($rows as $row) {
                Fact::query()->create([
                    'fact_title' => $row['title'],
                    'fact_summary' => $row['summary'],
                    'fact_description' => $row['description'],
                    'is_ai_managed' => true,
                ]);
            }
        });

        Cache::forget('facts');
        Cache::put('facts_last_ai_refresh', [
            'completed_at' => now()->toIso8601String(),
            'facts_count' => count($rows),
            'source' => $source,
        ], now()->addDays(366));
    }

    public function failed(\Throwable $e): void
    {
        Log::error('RefreshAfricaHealthFactsJob failed: '.$e->getMessage(), ['exception' => $e]);
    }
}
