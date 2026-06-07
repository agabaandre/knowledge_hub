<?php

namespace App\Services;

use App\Models\CourseSyncRun;

class CourseSyncRunService
{
    public function create(?int $userId = null): CourseSyncRun
    {
        return CourseSyncRun::query()->create([
            'user_id' => $userId,
            'status' => 'queued',
            'progress' => 0,
            'step' => 'Queued',
            'message' => 'Waiting to fetch courses from learning platforms…',
            'courses_fetched' => 0,
            'courses_total' => 0,
        ]);
    }

    public function markRunning(CourseSyncRun $run, string $step, int $progress = 5): CourseSyncRun
    {
        $run->update([
            'status' => 'running',
            'progress' => max(0, min(100, $progress)),
            'step' => $step,
            'started_at' => $run->started_at ?? now(),
        ]);

        return $run->fresh();
    }

    public function updateProgress(
        CourseSyncRun $run,
        int $progress,
        string $step,
        ?string $message = null,
        int $coursesFetched = 0,
        int $coursesTotal = 0
    ): CourseSyncRun {
        $run->update([
            'status' => 'running',
            'progress' => max(0, min(100, $progress)),
            'step' => $step,
            'message' => $message,
            'courses_fetched' => max(0, $coursesFetched),
            'courses_total' => max(0, $coursesTotal),
        ]);

        return $run->fresh();
    }

    public function complete(CourseSyncRun $run, string $message, array $result = []): CourseSyncRun
    {
        $total = (int) ($result['total'] ?? $run->courses_fetched);

        $run->update([
            'status' => 'completed',
            'progress' => 100,
            'step' => 'Complete',
            'message' => $message,
            'courses_fetched' => $total,
            'courses_total' => $total,
            'result' => $result ?: null,
            'finished_at' => now(),
        ]);

        return $run->fresh();
    }

    public function fail(CourseSyncRun $run, string $message, array $result = []): CourseSyncRun
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

    public function toStatusArray(CourseSyncRun $run): array
    {
        return [
            'id' => $run->id,
            'status' => $run->status,
            'progress' => (int) $run->progress,
            'step' => $run->step,
            'message' => $run->message,
            'courses_fetched' => (int) $run->courses_fetched,
            'courses_total' => (int) $run->courses_total,
            'finished' => $run->isFinished(),
            'alert_type' => $run->status === 'completed' ? 'success' : ($run->status === 'failed' ? 'danger' : 'info'),
        ];
    }
}
