<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ContributorBadgeAwardService;
use Illuminate\Console\Command;

class RecalculateLifetimeBadges extends Command
{
    protected $signature = 'badges:recalculate-lifetime {--user= : Optional user ID}';

    protected $description = 'Recalculate lifetime contributor badges from all hub contributions (not community-scoped)';

    public function handle(ContributorBadgeAwardService $service): int
    {
        $userId = $this->option('user');

        $query = User::query()->orderBy('id');
        if ($userId) {
            $query->whereKey((int) $userId);
        }

        $upgraded = 0;
        $withBadge = 0;

        $query->chunkById(200, function ($users) use ($service, &$upgraded, &$withBadge) {
            foreach ($users as $user) {
                $result = $service->recalculateLifetimeBadge($user);
                if ($result['badge_type']) {
                    $withBadge++;
                }
                if ($result['upgraded']) {
                    $upgraded++;
                    $this->line("Upgraded: {$user->name} → {$result['badge_type']->name} ({$result['lifetime_contributions']})");
                }
            }
        });

        $this->info("Done. {$withBadge} user(s) with a badge tier; {$upgraded} upgraded.");

        return 0;
    }
}
