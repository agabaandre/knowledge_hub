<?php

namespace App\Services\Owid;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OwidApiClient
{
    public function searchCharts(
        ?string $query = null,
        ?string $topic = null,
        int $page = 0,
        int $hitsPerPage = 20
    ): array {
        $params = [
            'type' => 'charts',
            'page' => max(0, $page),
            'hitsPerPage' => min(100, max(1, $hitsPerPage)),
        ];

        if ($query !== null && trim($query) !== '') {
            $params['q'] = trim($query);
        }

        if ($topic !== null && trim($topic) !== '') {
            $params['topics'] = trim($topic);
        }

        $response = Http::timeout(config('owid.timeout', 30))
            ->acceptJson()
            ->get($this->searchUrl(), $params);

        if (! $response->successful()) {
            $message = (string) ($response->json('message') ?? $response->body());
            throw new RuntimeException('OWID search failed: HTTP '.$response->status().($message !== '' ? ' — '.$message : ''));
        }

        return $response->json() ?? [];
    }

    /**
     * Try topic filter first, then fall back to query-only search when OWID rejects a topic.
     */
    public function searchChartsForSubject(
        ?string $topic,
        ?string $searchQuery,
        int $hitsPerPage = 20
    ): array {
        $attempts = [];

        if ($topic) {
            $attempts[] = ['topic' => $topic, 'query' => null];
        }
        if ($searchQuery) {
            if ($topic) {
                $attempts[] = ['topic' => $topic, 'query' => $searchQuery];
            }
            $attempts[] = ['topic' => null, 'query' => $searchQuery];
        }

        $lastError = null;

        foreach ($attempts as $attempt) {
            try {
                return $this->searchCharts(
                    query: $attempt['query'],
                    topic: $attempt['topic'],
                    hitsPerPage: $hitsPerPage
                );
            } catch (RuntimeException $e) {
                $lastError = $e;
                Log::debug('owid.search_attempt_failed', [
                    'topic' => $attempt['topic'],
                    'query' => $attempt['query'],
                    'message' => $e->getMessage(),
                ]);
            }
        }

        throw $lastError ?? new RuntimeException('OWID search failed: no topic or query configured.');
    }

    public function fetchChartCsv(string $slug): string
    {
        $url = $this->baseUrl().'/grapher/'.rawurlencode($slug).'.csv';

        $response = Http::timeout(config('owid.timeout', 30))
            ->withHeaders(['Accept' => 'text/csv'])
            ->get($url, [
                'v' => 1,
                'downloadFormat' => 'csv',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('OWID chart CSV failed for '.$slug.': HTTP '.$response->status());
        }

        return (string) $response->body();
    }

    /**
     * @param  array<int, string>  $iso3Codes  Uppercase ISO3 codes to keep
     * @return array<string, array{entity: string, iso3: string, year: int, value: float|null, period: string}>
     */
    public function parseLatestValuesByIso3(string $csv, array $iso3Codes): array
    {
        $wanted = array_fill_keys(array_map('strtoupper', $iso3Codes), true);
        $latest = [];

        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            throw new RuntimeException('Unable to parse OWID CSV.');
        }

        fwrite($handle, $csv);
        rewind($handle);

        $header = fgetcsv($handle);
        if (! is_array($header) || count($header) < 4) {
            fclose($handle);

            return [];
        }

        $valueColumn = $header[3] ?? 'value';

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                continue;
            }

            [$entity, $code, $year, $value] = $row;
            $iso3 = strtoupper(trim((string) $code));
            if ($iso3 === '' || ! isset($wanted[$iso3])) {
                continue;
            }

            $yearInt = (int) $year;
            if ($yearInt <= 0) {
                continue;
            }

            $numeric = is_numeric($value) ? (float) $value : null;
            if ($numeric === null) {
                continue;
            }

            if (! isset($latest[$iso3]) || $yearInt >= $latest[$iso3]['year']) {
                $latest[$iso3] = [
                    'entity' => trim((string) $entity),
                    'iso3' => $iso3,
                    'year' => $yearInt,
                    'value' => $numeric,
                    'period' => sprintf('%04d-01', $yearInt),
                    'value_column' => $valueColumn,
                ];
            }
        }

        fclose($handle);

        return $latest;
    }

    protected function searchUrl(): string
    {
        return rtrim((string) config('owid.base_url', 'https://ourworldindata.org'), '/')
            .config('owid.search_path', '/api/search');
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('owid.base_url', 'https://ourworldindata.org'), '/');
    }
}
