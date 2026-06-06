<?php

namespace App\Console\Commands;

use App\Jobs\GenerateKpiNarrationsJob;
use App\Services\Owid\OwidIndicatorSyncService;
use Illuminate\Console\Command;

class ApproveDefaultOwidKpiCommand extends Command
{
    protected $signature = 'kpi:approve-owid-defaults {--narrations : Queue AI narrations for approved indicators}';

    protected $description = 'Approve the curated default Our World in Data indicators for public country pages';

    public function handle(OwidIndicatorSyncService $sync): int
    {
        $result = $sync->approveDefaultIndicators(null);

        $this->info(sprintf(
            'Approved %d indicators (%d already published, %d not yet discovered).',
            $result['approved'],
            $result['already_published'],
            $result['missing']
        ));

        foreach ($result['errors'] as $error) {
            $this->warn($error);
        }

        if ($this->option('narrations')) {
            $queued = 0;
            foreach ($result['kpi_ids'] as $kpiId) {
                GenerateKpiNarrationsJob::dispatch($kpiId);
                $queued++;
            }
            $this->info(sprintf('Queued narration generation for %d indicators.', $queued));
        }

        return self::SUCCESS;
    }
}
