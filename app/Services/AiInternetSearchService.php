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

        $results = [];

        if (AiConfig::serperAvailable()) {
            $apiKey = AiConfig::serperApiKey();
            $results = array_merge($results, $this->searchViaSerperScholar($query, min(2, $limit), $apiKey));
            if (count($results) < $limit) {
                $results = array_merge(
                    $results,
                    $this->searchViaSerperWeb($query, $limit - count($results), $apiKey, 'site:pubmed.ncbi.nlm.nih.gov')
                );
            }
        }

        if (count($results) < $limit) {
            $results = array_merge(
                $results,
                $this->searchViaDuckDuckGo($query.' site:pubmed.ncbi.nlm.nih.gov', $limit - count($results))
            );
        }

        $results = $this->uniqueResults($results);

        if (count($results) >= $limit) {
            return array_slice($results, 0, $limit);
        }

        foreach ($this->scholarlyFallbackResults($query, $limit) as $fallback) {
            $results[] = $fallback;
            $results = $this->uniqueResults($results);
            if (count($results) >= $limit) {
                break;
            }
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function searchViaSerperScholar(string $query, int $limit, string $apiKey): array
    {
        if ($limit < 1) {
            return [];
        }

        try {
            $response = Http::timeout(12)
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post('https://google.serper.dev/scholar', [
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
                    (string) ($row['snippet'] ?? ($row['publicationInfo'] ?? '')),
                    'google_scholar'
                );
                if ($normalized !== null && $this->isScholarlyUrl($normalized['url'])) {
                    $results[] = $normalized;
                }
                if (count($results) >= $limit) {
                    break;
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::debug('ai_internet_search.serper_scholar_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function searchViaSerperWeb(string $query, int $limit, string $apiKey, ?string $queryPrefix = null): array
    {
        if ($limit < 1) {
            return [];
        }

        try {
            $searchQuery = $queryPrefix ? trim($queryPrefix.' '.$query) : $query;
            $response = Http::timeout(12)
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post('https://google.serper.dev/search', [
                    'q' => $searchQuery,
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
                $url = (string) ($row['link'] ?? '');
                $source = str_contains(strtolower($url), 'pubmed') ? 'pubmed' : 'google_scholar';
                $normalized = $this->normalizeResult(
                    (string) ($row['title'] ?? ''),
                    $url,
                    (string) ($row['snippet'] ?? ''),
                    $source
                );
                if ($normalized !== null && $this->isScholarlyUrl($normalized['url'])) {
                    $results[] = $normalized;
                }
                if (count($results) >= $limit) {
                    break;
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::debug('ai_internet_search.serper_web_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function searchViaDuckDuckGo(string $query, int $limit): array
    {
        if ($limit < 1) {
            return [];
        }

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
                    if (! $this->isScholarlyUrl($url)) {
                        continue;
                    }
                    $title = $this->cleanHtmlFragment($match[2]);
                    $source = str_contains(strtolower($url), 'pubmed') ? 'pubmed' : 'google_scholar';
                    $normalized = $this->normalizeResult($title, $url, '', $source);
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
    private function scholarlyFallbackResults(string $query, int $limit): array
    {
        $encoded = rawurlencode($query);

        $candidates = [
            [
                'title' => 'Google Scholar — '.$query,
                'url' => 'https://scholar.google.com/scholar?q='.$encoded,
                'snippet' => 'Peer-reviewed articles, theses, books, and conference papers.',
                'source' => 'google_scholar',
            ],
            [
                'title' => 'PubMed — '.$query,
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/?term='.$encoded,
                'snippet' => 'Biomedical and life sciences literature from MEDLINE and related databases.',
                'source' => 'pubmed',
            ],
        ];

        return array_slice($candidates, 0, max(1, $limit));
    }

    /**
     * @param  list<array{title: string, url: string, snippet: string, source: string}>  $results
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function uniqueResults(array $results): array
    {
        $unique = [];
        $seen = [];
        foreach ($results as $row) {
            $key = mb_strtolower($row['url'] ?? '');
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $row;
        }

        return $unique;
    }

    private function isScholarlyUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return str_contains($host, 'scholar.google')
            || str_contains($host, 'pubmed.ncbi.nlm.nih.gov')
            || str_contains($host, 'ncbi.nlm.nih.gov');
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
            $title = $source === 'pubmed' ? 'PubMed' : 'Google Scholar';
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
