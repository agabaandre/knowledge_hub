<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Kpi;
use App\Models\KpiNarration;
use Illuminate\Support\Facades\Log;

class KpiNarrationService
{
    public function __construct(private ChatGPTService $chatGpt)
    {
    }

    public function narrationFor(Kpi $kpi, Country $country, string $period, float $value): ?string
    {
        $existing = KpiNarration::query()
            ->where('kpi_id', $kpi->id)
            ->where('country_id', $country->id)
            ->where('period', $period)
            ->first();

        if ($existing) {
            return $existing->narration;
        }

        if (empty(config('ai.open_api_key'))) {
            return null;
        }

        try {
            $year = substr($period, 0, 4);
            $unit = $kpi->unit_label ? ' '.$kpi->unit_label : '';
            $prompt = <<<PROMPT
Write 2 concise sentences for a public health knowledge hub member-state profile.
Country: {$country->name}
Indicator: {$kpi->name}
Latest value: {$value}{$unit} ({$year})
Context: {$kpi->description}
Source: Our World in Data (CC BY 4.0)

Use plain language suitable for policy audiences in Africa. Do not invent numbers. No markdown.
PROMPT;

            $text = trim(strip_tags($this->chatGpt->chatMessagesComplete([
                ['role' => 'system', 'content' => 'You write brief factual indicator narrations for Africa CDC Knowledge Hub country pages.'],
                ['role' => 'user', 'content' => $prompt],
            ])));

            if ($text === ''
                || str_contains($text, 'alert alert-danger')
                || str_contains(strtolower($text), 'no response from ai')) {
                return null;
            }

            KpiNarration::query()->updateOrCreate(
                [
                    'kpi_id' => $kpi->id,
                    'country_id' => $country->id,
                    'period' => $period,
                ],
                [
                    'narration' => $text,
                    'ai_model' => config('ai.openai_model', 'gpt-3.5-turbo'),
                    'generated_at' => now(),
                ]
            );

            return $text;
        } catch (\Throwable $e) {
            Log::warning('kpi.narration_failed', [
                'kpi_id' => $kpi->id,
                'country_id' => $country->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
