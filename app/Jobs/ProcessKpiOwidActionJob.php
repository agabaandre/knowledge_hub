<?php

namespace App\Jobs;

use App\Models\Kpi;
use App\Models\KpiSyncRun;
use App\Services\Kpi\KpiDeduplicationService;
use App\Services\Kpi\KpiSyncRunService;
use App\Services\Owid\OwidIndicatorSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessKpiOwidActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;

    public function __construct(public int $runId)
    {
    }

    public function handle(
        OwidIndicatorSyncService $sync,
        KpiSyncRunService $runs,
        KpiDeduplicationService $dedupe
    ): void {
        $run = KpiSyncRun::query()->find($this->runId);
        if (! $run || $run->isFinished()) {
            return;
        }

        $runs->markRunning($run, 'Starting…', 5);

        try {
            match ($run->action) {
                'discover' => $this->runDiscover($run, $sync, $runs),
                'sync' => $this->runSync($run, $sync, $runs),
                'fresh_fetch' => $this->runFreshFetch($run, $sync, $runs),
                'approve_defaults' => $this->runApproveDefaults($run, $sync, $runs),
                'generate_narrations' => $this->runGenerateNarrations($run, $runs),
                'sync_one' => $this->runSyncOne($run, $sync, $runs),
                'approve' => $this->runApprove($run, $sync, $runs),
                'dedupe_indicators' => $this->runDedupeIndicators($run, $dedupe, $runs),
                'dedupe_subject_areas' => $this->runDedupeSubjectAreas($run, $dedupe, $runs),
                default => throw new \RuntimeException('Unknown KPI task action: '.$run->action),
            };
        } catch (Throwable $e) {
            $runs->fail($run, $e->getMessage());
        }
    }

    protected function runDiscover(KpiSyncRun $run, OwidIndicatorSyncService $sync, KpiSyncRunService $runs): void
    {
        $subjectAreaId = isset($run->payload['subject_area_id']) ? (int) $run->payload['subject_area_id'] : null;

        $result = $sync->discoverIndicators($subjectAreaId, function (int $current, int $total, string $label) use ($run, $runs) {
            $progress = (int) round(($current / max(1, $total)) * 90);
            $runs->updateProgress($run, $progress, $label);
        });

        $message = sprintf(
            'Discovered %d indicators across %d subject areas (%d skipped, %d duplicates).',
            $result['discovered'],
            $result['subject_areas'],
            $result['skipped'],
            $result['duplicates'] ?? 0
        );
        if (! empty($result['errors'])) {
            $message .= ' Warnings: '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $runs->updateProgress($run, 92, 'Checking for duplicate indicators…');
        $dedupeResult = app(KpiDeduplicationService::class)->autoDedupeIndicators(function (int $current, int $total, string $label) use ($run, $runs) {
            $runs->updateProgress($run, 92 + (int) round(($current / max(1, $total)) * 6), $label);
        });
        if (($dedupeResult['removed'] ?? 0) > 0) {
            $message .= sprintf(' Auto-merged %d duplicate indicator(s).', $dedupeResult['removed']);
        }

        $runs->complete($run, $message, array_merge($result, ['dedupe' => $dedupeResult]));
    }

    protected function runSync(KpiSyncRun $run, OwidIndicatorSyncService $sync, KpiSyncRunService $runs): void
    {
        $publishedOnly = (bool) ($run->payload['published_only'] ?? false);
        $kpiId = isset($run->payload['kpi_id']) ? (int) $run->payload['kpi_id'] : null;

        $runs->updateProgress($run, 10, 'Downloading country values from Our World in Data…');

        $result = $sync->syncIndicatorData($kpiId, $publishedOnly, function (int $current, int $total, string $label) use ($run, $runs) {
            $progress = 10 + (int) round(($current / max(1, $total)) * 85);
            $runs->updateProgress($run, $progress, $label);
        });

        $message = sprintf('Synced %d indicators (%d country values).', $result['indicators'], $result['rows']);
        if (! empty($result['errors'])) {
            $message .= ' Notes: '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $runs->complete($run, $message, $result);
    }

    protected function runFreshFetch(KpiSyncRun $run, OwidIndicatorSyncService $sync, KpiSyncRunService $runs): void
    {
        $runs->updateProgress($run, 5, 'Fetching new indicators…');
        $discover = $sync->discoverIndicators(null, function (int $current, int $total, string $label) use ($run, $runs) {
            $progress = 5 + (int) round(($current / max(1, $total)) * 40);
            $runs->updateProgress($run, $progress, $label);
        });

        $runs->updateProgress($run, 50, 'Refreshing published country values…');
        $syncResult = $sync->syncIndicatorData(null, true, function (int $current, int $total, string $label) use ($run, $runs) {
            $progress = 50 + (int) round(($current / max(1, $total)) * 45);
            $runs->updateProgress($run, $progress, $label);
        });

        $message = sprintf(
            'Full refresh complete. Fetched %d new indicators; refreshed %d published indicators (%d country values).',
            $discover['discovered'],
            $syncResult['indicators'],
            $syncResult['rows']
        );

        $errors = array_merge($discover['errors'] ?? [], $syncResult['errors']);
        if (! empty($errors)) {
            $message .= ' Notes: '.implode(' | ', array_slice($errors, 0, 3));
        }

        $runs->updateProgress($run, 96, 'Checking for duplicate indicators…');
        $dedupeIndicators = app(KpiDeduplicationService::class)->autoDedupeIndicators();
        $dedupeAreas = app(KpiDeduplicationService::class)->autoDedupeSubjectAreas();
        if (($dedupeIndicators['removed'] ?? 0) > 0 || ($dedupeAreas['removed'] ?? 0) > 0) {
            $message .= sprintf(
                ' Auto-merged %d duplicate indicator(s) and %d subject area(s).',
                $dedupeIndicators['removed'] ?? 0,
                $dedupeAreas['removed'] ?? 0
            );
        }

        $runs->complete($run, $message, [
            'discover' => $discover,
            'sync' => $syncResult,
            'dedupe_indicators' => $dedupeIndicators,
            'dedupe_subject_areas' => $dedupeAreas,
        ]);
    }

    protected function runApproveDefaults(KpiSyncRun $run, OwidIndicatorSyncService $sync, KpiSyncRunService $runs): void
    {
        $userId = isset($run->payload['user_id']) ? (int) $run->payload['user_id'] : null;
        $withNarrations = (bool) ($run->payload['narrations'] ?? false);

        $result = $sync->approveDefaultIndicators($userId, function (int $current, int $totalSlugs, string $label) use ($run, $runs) {
            $progress = 5 + (int) round(($current / max(1, $totalSlugs)) * 90);
            $runs->updateProgress($run, $progress, $label);
        });

        if ($withNarrations) {
            foreach ($result['kpi_ids'] as $kpiId) {
                GenerateKpiNarrationsJob::dispatch((int) $kpiId);
            }
        }

        $message = sprintf(
            'Published %d recommended indicators (%d already live, %d not yet discovered).',
            $result['approved'],
            $result['already_published'],
            $result['missing']
        );
        if (! empty($result['errors'])) {
            $message .= ' Notes: '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $runs->complete($run, $message, $result);
    }

    protected function runGenerateNarrations(KpiSyncRun $run, KpiSyncRunService $runs): void
    {
        $kpiIds = Kpi::query()->where('status', 'published')->pluck('id');
        $total = max(1, $kpiIds->count());

        foreach ($kpiIds as $index => $kpiId) {
            GenerateKpiNarrationsJob::dispatch((int) $kpiId);
            $runs->updateProgress(
                $run,
                5 + (int) round((($index + 1) / $total) * 90),
                'Queueing summaries…',
                sprintf('Queued %d of %d indicators', $index + 1, $total)
            );
        }

        $runs->complete(
            $run,
            sprintf('Queued AI summary generation for %d published indicators.', $kpiIds->count()),
            ['queued' => $kpiIds->count()]
        );
    }

    protected function runSyncOne(KpiSyncRun $run, OwidIndicatorSyncService $sync, KpiSyncRunService $runs): void
    {
        $kpiId = (int) ($run->payload['kpi_id'] ?? 0);
        $kpi = Kpi::query()->findOrFail($kpiId);

        $runs->updateProgress($run, 20, 'Refreshing '.$kpi->name.'…');

        $result = $sync->syncIndicatorData($kpiId, false, function () use ($run, $runs, $kpi) {
            $runs->updateProgress($run, 60, 'Syncing '.$kpi->name.'…');
        });

        $message = sprintf('Refreshed "%s" with %d country values.', $kpi->name, $result['rows']);
        if (! empty($result['errors'])) {
            $message .= ' '.$result['errors'][0];
        }

        $runs->complete($run, $message, $result);
    }

    protected function runApprove(KpiSyncRun $run, OwidIndicatorSyncService $sync, KpiSyncRunService $runs): void
    {
        $kpiId = (int) ($run->payload['kpi_id'] ?? 0);
        $userId = isset($run->payload['user_id']) ? (int) $run->payload['user_id'] : null;
        $withNarrations = (bool) ($run->payload['narrations'] ?? true);

        $kpi = Kpi::query()->findOrFail($kpiId);
        $runs->updateProgress($run, 20, 'Publishing '.$kpi->name.'…');

        $sync->approve($kpi, $userId);

        if ($withNarrations) {
            GenerateKpiNarrationsJob::dispatch($kpiId);
        }

        $runs->complete($run, 'Indicator approved and country data synced from Our World in Data.', [
            'kpi_id' => $kpiId,
        ]);
    }

    protected function runDedupeIndicators(KpiSyncRun $run, KpiDeduplicationService $dedupe, KpiSyncRunService $runs): void
    {
        $result = $dedupe->autoDedupeIndicators(function (int $current, int $total, string $label) use ($run, $runs) {
            $progress = 5 + (int) round(($current / max(1, $total)) * 90);
            $runs->updateProgress($run, $progress, $label);
        });

        $message = sprintf(
            'Merged duplicate indicators in %d group(s); removed %d duplicate record(s).',
            $result['merged'],
            $result['removed']
        );
        if (! empty($result['errors'])) {
            $message .= ' Notes: '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $runs->complete($run, $message, $result);
    }

    protected function runDedupeSubjectAreas(KpiSyncRun $run, KpiDeduplicationService $dedupe, KpiSyncRunService $runs): void
    {
        $result = $dedupe->autoDedupeSubjectAreas(function (int $current, int $total, string $label) use ($run, $runs) {
            $progress = 5 + (int) round(($current / max(1, $total)) * 90);
            $runs->updateProgress($run, $progress, $label);
        });

        $message = sprintf(
            'Merged duplicate subject areas in %d group(s); removed %d duplicate record(s).',
            $result['merged'],
            $result['removed']
        );
        if (! empty($result['errors'])) {
            $message .= ' Notes: '.implode(' | ', array_slice($result['errors'], 0, 3));
        }

        $runs->complete($run, $message, $result);
    }
}
