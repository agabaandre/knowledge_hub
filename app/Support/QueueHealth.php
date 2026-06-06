<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;

class QueueHealth
{
    /**
     * @return array{
     *     driver: string,
     *     driver_ok: bool,
     *     pending_jobs: ?int,
     *     failed_jobs: int,
     *     worker_hint: string,
     *     error: ?string
     * }
     */
    public static function snapshot(): array
    {
        $driver = (string) config('queue.default', 'sync');
        $failed = 0;
        $pending = null;
        $driverOk = true;
        $error = null;

        try {
            if (Schema::hasTable('failed_jobs')) {
                $failed = (int) DB::table('failed_jobs')->count();
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        try {
            if ($driver === 'redis') {
                $pending = (int) Redis::connection()->llen('queues:default');
            } elseif ($driver === 'database' && Schema::hasTable('jobs')) {
                $pending = (int) DB::table('jobs')->count();
            } elseif ($driver === 'sync') {
                $pending = 0;
            }
        } catch (\Throwable $e) {
            $driverOk = false;
            $error = $error ?: $e->getMessage();
        }

        $workerHint = $driver === 'sync'
            ? 'Queue driver is sync — jobs run immediately in the web/CLI process (no background worker).'
            : 'Ensure a worker is running: php artisan queue:work redis --queue=default (or the queue Docker service).';

        return [
            'driver' => $driver,
            'driver_ok' => $driverOk,
            'pending_jobs' => $pending,
            'failed_jobs' => $failed,
            'worker_hint' => $workerHint,
            'error' => $error,
        ];
    }
}
