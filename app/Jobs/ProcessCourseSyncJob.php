<?php

namespace App\Jobs;

use App\Models\CourseSyncRun;
use App\Services\CourseSyncRunService;
use App\Services\CourseSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessCourseSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;

    public function __construct(public int $runId)
    {
    }

    public function handle(CourseSyncService $syncService, CourseSyncRunService $runs): void
    {
        $run = CourseSyncRun::query()->find($this->runId);
        if (! $run || $run->isFinished()) {
            return;
        }

        $runs->markRunning($run, 'Starting course fetch…', 5);

        try {
            $result = $syncService->syncAll(null, function (
                int $progress,
                string $step,
                ?string $message,
                int $coursesFetched,
                int $coursesTotal
            ) use ($run, $runs) {
                $runs->updateProgress($run, $progress, $step, $message, $coursesFetched, $coursesTotal);
            });

            $total = (int) ($result['total'] ?? 0);
            $providers = array_keys($result['providers'] ?? []);
            $skipped = $result['skipped'] ?? [];

            if ($providers === []) {
                $runs->fail($run, 'No learning platforms are configured and enabled for sync. Check Admin → Configure → Advanced.');

                return;
            }

            $message = $total > 0
                ? sprintf('Fetched %d course(s) from %s.', $total, implode(', ', $providers))
                : 'Sync finished. No courses were returned by the connected platform(s).';

            if ($skipped !== []) {
                $message .= ' Skipped: '.implode(', ', $skipped).'.';
            }

            $runs->complete($run, $message, $result);
        } catch (Throwable $e) {
            $runs->fail($run, $e->getMessage());
        }
    }
}
