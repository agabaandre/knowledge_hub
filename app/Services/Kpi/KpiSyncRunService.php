<?php

namespace App\Services\Kpi;

use App\Models\KpiSyncRun;

class KpiSyncRunService
{
    public function create(string $action, array $payload = [], ?int $userId = null): KpiSyncRun
    {
        return KpiSyncRun::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'status' => 'queued',
            'progress' => 0,
            'step' => 'Queued',
            'message' => 'Waiting for background worker…',
            'payload' => $payload ?: null,
        ]);
    }

    public function markRunning(KpiSyncRun $run, string $step, int $progress = 5): KpiSyncRun
    {
        $run->update([
            'status' => 'running',
            'progress' => max(0, min(100, $progress)),
            'step' => $step,
            'started_at' => $run->started_at ?? now(),
        ]);

        return $run->fresh();
    }

    public function updateProgress(KpiSyncRun $run, int $progress, string $step, ?string $message = null): KpiSyncRun
    {
        $run->update([
            'status' => 'running',
            'progress' => max(0, min(100, $progress)),
            'step' => $step,
            'message' => $message,
        ]);

        return $run->fresh();
    }

    public function complete(KpiSyncRun $run, string $message, array $result = []): KpiSyncRun
    {
        $run->update([
            'status' => 'completed',
            'progress' => 100,
            'step' => 'Complete',
            'message' => $message,
            'result' => $result ?: null,
            'finished_at' => now(),
        ]);

        return $run->fresh();
    }

    public function fail(KpiSyncRun $run, string $message, array $result = []): KpiSyncRun
    {
        $run->update([
            'status' => 'failed',
            'step' => 'Failed',
            'message' => $message,
            'result' => $result ?: null,
            'finished_at' => now(),
        ]);

        return $run->fresh();
    }

    public function toStatusArray(KpiSyncRun $run): array
    {
        return [
            'id' => $run->id,
            'action' => $run->action,
            'status' => $run->status,
            'progress' => (int) $run->progress,
            'step' => $run->step,
            'message' => $run->message,
            'finished' => $run->isFinished(),
            'alert_type' => $run->status === 'completed' ? 'success' : ($run->status === 'failed' ? 'danger' : 'info'),
        ];
    }
}
