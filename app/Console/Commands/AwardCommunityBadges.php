<?php

namespace App\Console\Commands;

use App\Jobs\AwardCommunityBadgesJob;
use App\Services\ContributorBadgeAwardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AwardCommunityBadges extends Command
{
    protected $signature = 'badges:award-community
                            {--month= : Month to process (1-12)}
                            {--year= : Year to process}
                            {--queue : Dispatch to the queue instead of running synchronously}';

    protected $description = 'Recalculate lifetime contributor badges and sync monthly community contribution snapshots';

    public function handle(ContributorBadgeAwardService $service): int
    {
        $period = $service->defaultPeriod();
        $year = (int) ($this->option('year') ?: $period['year']);
        $month = (int) ($this->option('month') ?: $period['month']);

        if ($this->option('queue')) {
            AwardCommunityBadgesJob::dispatch($year, $month, 'cli:queue');
            $this->info("Queued badge awarding for {$year}-".str_pad((string) $month, 2, '0', STR_PAD_LEFT).' on the default queue.');

            return 0;
        }

        $this->info('Processing badges for '.$year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT));

        $result = $service->awardForPeriod($year, $month, 'cli');

        Cache::put('badges_last_award_run', $result, now()->addDays(120));

        if ($result['status'] === 'failed') {
            $this->error('Badge awarding failed: '.($result['error'] ?? 'unknown error'));

            return 1;
        }

        if ($result['status'] === 'skipped') {
            $this->warn($result['error'] ?? 'Skipped.');

            return 0;
        }

        $this->info('Badge awarding completed.');
        $this->line('  Users processed: '.($result['users_processed'] ?? 0));
        $this->line('  New lifetime badges: '.$result['badges_awarded']);
        $this->line('  Badge upgrades: '.($result['badges_upgraded'] ?? 0));
        $this->line('  Community monthly rows synced: '.($result['community_rows_synced'] ?? 0));
        $this->line('  Notification emails queued: '.$result['emails_queued']);

        return 0;
    }
}
