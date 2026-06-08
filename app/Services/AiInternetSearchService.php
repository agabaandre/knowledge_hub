<?php

namespace App\Services;

use App\Support\AiConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiInternetSearchService
{
    /**
     * @return list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>
     */
    public function search(string $query, int $limit = 3): array
    {
        $query = trim($query);
        if ($query === '' || $limit < 1) {
            return [];
        }

        $sites = AiConfig::enabledAiSearchSites();
        if ($sites === []) {
            return [];
        }

        $results = [];

        if (AiConfig::serperAvailable()) {
            $apiKey = AiConfig::serperApiKey();

            foreach ($sites as $site) {
                if (count($results) >= $limit) {
                    break;
                }
                if (($site['serper_mode'] ?? '') !== 'scholar') {
                    continue;
                }
                $results = array_merge(
                    $results,
                    $this->searchViaSerperScholar($query, min(2, $limit - count($results)), $apiKey)
                );
            }

            foreach ($sites as $site) {
                if (count($results) >= $limit) {
                    break;
                }
                if (($site['serper_mode'] ?? '') !== 'web' || trim((string) ($site['site_search'] ?? '')) === '') {
                    continue;
                }
                $results = array_merge(
                    $results,
                    $this->searchViaSerperWeb(
                        $query,
                        $limit - count($results),
                        $apiKey,
                        (string) $site['site_search'],
                        $site
                    )
                );
            }
        }

        if (count($results) < $limit) {
            foreach ($sites as $site) {
                if (count($results) >= $limit) {
                    break;
                }
                $siteSearch = trim((string) ($site['site_search'] ?? ''));
                if (($site['serper_mode'] ?? '') !== 'web' || $siteSearch === '') {
                    continue;
                }
                $results = array_merge(
                    $results,
                    $this->searchViaDuckDuckGo(trim($query.' '.$siteSearch), $limit - count($results), $site)
                );
            }
        }

        $results = $this->uniqueResults($results);

        if (count($results) >= $limit) {
            return array_slice($results, 0, $limit);
        }

        foreach ($this->fallbackResults($query, $sites, $limit) as $fallback) {
            $results[] = $fallback;
            $results = $this->uniqueResults($results);
            if (count($results) >= $limit) {
                break;
            }
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>
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
                    (string) ($row['snippet'] ?? ($row['publicationInfo'] ?? ''))
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
            Log::debug('ai_internet_search.serper_scholar_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @param  array<string, mixed>  $site
     * @return list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>
     */
    private function searchViaSerperWeb(string $query, int $limit, string $apiKey, string $queryPrefix, array $site): array
    {
        if ($limit < 1) {
            return [];
        }

        try {
            $searchQuery = trim($queryPrefix.' '.$query);
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
                $normalized = $this->normalizeResult(
                    (string) ($row['title'] ?? ''),
                    (string) ($row['link'] ?? ''),
                    (string) ($row['snippet'] ?? ''),
                    $site
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
            Log::debug('ai_internet_search.serper_web_failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @param  array<string, mixed>  $site
     * @return list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>
     */
    private function searchViaDuckDuckGo(string $query, int $limit, array $site): array
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
                    if (! AiConfig::urlMatchesAllowedSearchSite($url)) {
                        continue;
                    }
                    $title = $this->cleanHtmlFragment($match[2]);
                    $normalized = $this->normalizeResult($title, $url, '', $site);
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
     * @param  list<array<string, mixed>>  $sites
     * @return list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>
     */
    private function fallbackResults(string $query, array $sites, int $limit): array
    {
        $encoded = rawurlencode($query);
        $candidates = [];

        foreach ($sites as $site) {
            $searchUrl = str_replace('{query}', $encoded, (string) ($site['search_url'] ?? ''));
            if ($searchUrl === '' || ! preg_match('#^https?://#i', $searchUrl)) {
                continue;
            }

            $label = trim((string) ($site['label'] ?? 'Resource'));
            $candidates[] = [
                'title' => $label.' — '.$query,
                'url' => $searchUrl,
                'snippet' => trim((string) ($site['snippet'] ?? '')),
                'source' => (string) ($site['id'] ?? 'external'),
                'label' => $label,
                'icon' => trim((string) ($site['icon'] ?? 'fa-link')) ?: 'fa-link',
            ];
        }

        return array_slice($candidates, 0, max(1, $limit));
    }

    /**
     * @param  list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>  $results
     * @return list<array{title: string, url: string, snippet: string, source: string, label: string, icon: string}>
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

    /**
     * @param  array<string, mixed>|null  $site
     * @return array{title: string, url: string, snippet: string, source: string, label: string, icon: string}|null
     */
    private function normalizeResult(string $title, string $url, string $snippet, ?array $site = null): ?array
    {
        $url = trim($url);
        if ($url === '' || ! preg_match('#^https?://#i', $url)) {
            return null;
        }

        if (! AiConfig::urlMatchesAllowedSearchSite($url)) {
            return null;
        }

        $resolvedSite = $site ?? AiConfig::resolveSiteForUrl($url);
        $label = trim((string) ($resolvedSite['label'] ?? 'Resource'));
        $source = (string) ($resolvedSite['id'] ?? 'external');
        $icon = trim((string) ($resolvedSite['icon'] ?? 'fa-link')) ?: 'fa-link';

        $title = Str::limit(trim($this->cleanHtmlFragment($title)), 120);
        if ($title === '') {
            $title = $label;
        }

        return [
            'title' => $title,
            'url' => $url,
            'snippet' => Str::limit(trim(strip_tags($snippet)), 200),
            'source' => $source,
            'label' => $label,
            'icon' => $icon,
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
