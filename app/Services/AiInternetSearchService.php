<?php

namespace App\Services;

use App\Support\AiConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiInternetSearchService
{
    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    public function search(string $query, int $limit = 3): array
    {
        $query = trim($query);
        if ($query === '' || $limit < 1) {
            return [];
        }

        if (AiConfig::serperAvailable()) {
            $results = $this->searchViaSerper($query, $limit, AiConfig::serperApiKey());
            if ($results !== []) {
                return $results;
            }
        }

        $results = $this->searchViaDuckDuckGo($query, $limit);
        if ($results !== []) {
            return $results;
        }

        return $this->authoritativeFallbackResults($query, $limit);
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function searchViaSerper(string $query, int $limit, string $apiKey): array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post('https://google.serper.dev/search', [
                    'q' => $query,
                    'num' => min($limit, 10),
                ]);

            if (! $response->successful()) {
                return [];
            }

            $organic = (array) ($response->json('organic') ?? []);
            $results = [];
            foreach ($organic as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $normalized = $this->normalizeResult(
                    (string) ($row['title'] ?? ''),
                    (string) ($row['link'] ?? ''),
                    (string) ($row['snippet'] ?? ''),
                    'serper'
                );
                if ($normalized !== null) {
                    $results[] = $normalized;
                }
                if (count($results) >= $limit) {
                    break;
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::debug('ai_internet_search.serper_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function searchViaDuckDuckGo(string $query, int $limit): array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders([
                    'User-Agent' => 'AfricaCDC-KnowledgeHub/1.0 (+https://africacdc.org)',
                ])
                ->asForm()
                ->post('https://html.duckduckgo.com/html/', [
                    'q' => $query,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $html = (string) $response->body();
            $results = [];

            if (preg_match_all(
                '/<a[^>]+class="[^"]*result__a[^"]*"[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/is',
                $html,
                $matches,
                PREG_SET_ORDER
            )) {
                foreach ($matches as $match) {
                    $url = $this->resolveDuckDuckGoRedirectUrl(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $title = $this->cleanHtmlFragment($match[2]);
                    $normalized = $this->normalizeResult($title, $url, '', 'duckduckgo');
                    if ($normalized !== null) {
                        $results[] = $normalized;
                    }
                    if (count($results) >= $limit) {
                        break;
                    }
                }
            }

            if ($results !== []) {
                return $results;
            }

            if (preg_match_all(
                '/<a[^>]+href="(https?:\/\/[^"]+)"[^>]*class="[^"]*result-link[^"]*"/is',
                $html,
                $altMatches,
                PREG_SET_ORDER
            )) {
                foreach ($altMatches as $match) {
                    $normalized = $this->normalizeResult('', $match[1], '', 'duckduckgo');
                    if ($normalized !== null) {
                        $results[] = $normalized;
                    }
                    if (count($results) >= $limit) {
                        break;
                    }
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::debug('ai_internet_search.duckduckgo_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function authoritativeFallbackResults(string $query, int $limit): array
    {
        $encoded = rawurlencode($query);
        $candidates = [
            [
                'title' => 'WHO — search: '.$query,
                'url' => 'https://www.who.int/search?query='.$encoded,
                'snippet' => 'World Health Organization resources related to this topic.',
                'source' => 'who',
            ],
            [
                'title' => 'Africa CDC — search: '.$query,
                'url' => 'https://africacdc.org/?s='.$encoded,
                'snippet' => 'Africa CDC news, guidance, and public health updates.',
                'source' => 'africa_cdc',
            ],
            [
                'title' => 'PubMed — '.$query,
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/?term='.$encoded,
                'snippet' => 'Peer-reviewed biomedical literature from PubMed.',
                'source' => 'pubmed',
            ],
        ];

        $who = app(WhoFactsheetFetcher::class)->fetchForTopic($query);
        if (($who['ok'] ?? false) && ! empty($who['references'])) {
            foreach ((array) $who['references'] as $ref) {
                if (! is_array($ref)) {
                    continue;
                }
                $normalized = $this->normalizeResult(
                    (string) ($ref['label'] ?? 'WHO resource'),
                    (string) ($ref['url'] ?? ''),
                    Str::limit((string) ($who['excerpt'] ?? ''), 180),
                    'who'
                );
                if ($normalized !== null) {
                    array_unshift($candidates, $normalized);
                }
            }
        }

        $unique = [];
        $seen = [];
        foreach ($candidates as $row) {
            $key = mb_strtolower($row['url']);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $row;
            if (count($unique) >= $limit) {
                break;
            }
        }

        return $unique;
    }

    /**
     * @return array{title: string, url: string, snippet: string, source: string}|null
     */
    private function normalizeResult(string $title, string $url, string $snippet, string $source): ?array
    {
        $url = trim($url);
        if ($url === '' || ! preg_match('#^https?://#i', $url)) {
            return null;
        }

        $title = Str::limit(trim($this->cleanHtmlFragment($title)), 120);
        if ($title === '') {
            $title = parse_url($url, PHP_URL_HOST) ?: 'Web result';
        }

        return [
            'title' => $title,
            'url' => $url,
            'snippet' => Str::limit(trim(strip_tags($snippet)), 200),
            'source' => $source,
        ];
    }

    private function resolveDuckDuckGoRedirectUrl(string $url): string
    {
        if (preg_match('/uddg=([^&]+)/', $url, $match)) {
            return urldecode($match[1]);
        }

        return $url;
    }

    private function cleanHtmlFragment(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
