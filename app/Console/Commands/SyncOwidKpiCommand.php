<?php

namespace App\Console\Commands;

use App\Services\Owid\OwidIndicatorSyncService;
use Illuminate\Console\Command;

class SyncOwidKpiCommand extends Command
{
    protected $signature = 'kpi:sync-owid
        {--discover : Discover new indicators from OWID first}
        {--sync : Sync chart data from OWID grapher CSV}
        {--published-only : With --sync, only sync published indicators}';

    protected $description = 'Discover and sync Our World in Data indicators for African member states';

    public function handle(OwidIndicatorSyncService $sync): int
    {
        if ($this->option('discover')) {
            $discovered = $sync->discoverIndicators();
            $this->info(sprintf('Discovered %d indicators (%d skipped).', $discovered['discovered'], $discovered['skipped']));
            foreach ($discovered['errors'] ?? [] as $error) {
                $this->warn($error);
            }
        }

        if ($this->option('sync') || $this->option('published-only')) {
            $result = $sync->syncIndicatorData(null, (bool) $this->option('published-only'));
            $this->info(sprintf('Synced %d indicators, %d values.', $result['indicators'], $result['rows']));

            foreach ($result['errors'] as $error) {
                $this->warn($error);
            }
        }

        return self::SUCCESS;
    }
}
