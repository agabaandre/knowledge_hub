<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\Kpi;
use App\Models\KpiDataRecord;
use App\Services\KpiNarrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateKpiNarrationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $kpiId)
    {
    }

    public function handle(KpiNarrationService $narrations): void
    {
        $kpi = Kpi::query()->find($this->kpiId);
        if (! $kpi || $kpi->status !== 'published') {
            return;
        }

        $rows = KpiDataRecord::query()
            ->where('kpi_id', $kpi->id)
            ->get();

        foreach ($rows as $row) {
            $country = Country::query()->find($row->country_id);
            if (! $country) {
                continue;
            }

            $narrations->narrationFor($kpi, $country, (string) $row->period, (float) $row->value);
        }
    }
}
