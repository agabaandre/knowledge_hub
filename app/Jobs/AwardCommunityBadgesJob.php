<?php

namespace App\Jobs;

use App\Services\CommunityBadgeAwardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AwardCommunityBadgesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;

    public int $tries = 1;

    protected ?int $year;

    protected ?int $month;

    protected string $triggeredBy;

    public function __construct(?int $year = null, ?int $month = null, string $triggeredBy = 'queue')
    {
        $this->year = $year;
        $this->month = $month;
        $this->triggeredBy = $triggeredBy;
        $this->onQueue('default');
    }

    public function handle(CommunityBadgeAwardService $service): void
    {
        $period = $service->defaultPeriod();
        $year = $this->year ?? $period['year'];
        $month = $this->month ?? $period['month'];

        Cache::put('badges_award_job_running', [
            'started_at' => now()->toIso8601String(),
            'year' => $year,
            'month' => $month,
            'triggered_by' => $this->triggeredBy,
        ], now()->addHours(2));

        $result = $service->awardForPeriod($year, $month, $this->triggeredBy);

        Cache::put('badges_last_award_run', $result, now()->addDays(120));
        Cache::forget('badges_award_job_running');

        Log::info('AwardCommunityBadgesJob finished', $result);
    }

    public function failed(\Throwable $exception): void
    {
        Cache::forget('badges_award_job_running');

        $payload = [
            'status' => 'failed',
            'year' => $this->year,
            'month' => $this->month,
            'period_label' => null,
            'badges_awarded' => 0,
            'emails_queued' => 0,
            'communities_processed' => 0,
            'triggered_by' => $this->triggeredBy,
            'finished_at' => now()->toIso8601String(),
            'error' => $exception->getMessage(),
        ];

        Cache::put('badges_last_award_run', $payload, now()->addDays(120));

        Log::error('AwardCommunityBadgesJob failed', [
            'message' => $exception->getMessage(),
            'triggered_by' => $this->triggeredBy,
        ]);
    }
}
