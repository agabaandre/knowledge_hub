<?php

namespace App\Services;

use App\Support\HealthTopicSourceCatalog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HealthTopicSourceFetcher
{
    /**
     * @param  list<string>  $sourceKeys
     * @return array<string, list<string>>
     */
    public function collectReferenceTopics(array $sourceKeys): array
    {
        $out = [];
        foreach ($sourceKeys as $key) {
            $key = (string) $key;
            if (! isset(HealthTopicSourceCatalog::sources()[$key])) {
                continue;
            }
            $out[$key] = $this->topicsForSource($key);
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public function topicsForSource(string $sourceKey): array
    {
        $meta = HealthTopicSourceCatalog::sources()[$sourceKey] ?? null;
        if (! $meta) {
            return [];
        }

        $topics = [];
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'AfricaCDC-KnowledgeHub/1.0 (+health-topic-import)'])
                ->get($meta['url']);
            if ($response->successful()) {
                $topics = $this->extractTopicsFromHtml((string) $response->body(), $sourceKey);
            }
        } catch (\Throwable $e) {
            Log::warning('HealthTopicSourceFetcher: fetch failed', [
                'source' => $sourceKey,
                'url' => $meta['url'],
                'error' => $e->getMessage(),
            ]);
        }

        if (count($topics) < 8) {
            $topics = array_merge($topics, HealthTopicSourceCatalog::fallbackTopics($sourceKey));
        }

        return $this->uniqueTopics($topics);
    }

    /**
     * @return list<string>
     */
    private function extractTopicsFromHtml(string $html, string $sourceKey): array
    {
        $topics = [];

        if (preg_match_all('/<a[^>]+href=["\'][^"\']+["\'][^>]*>(.*?)<\/a>/is', $html, $matches)) {
            foreach ($matches[1] as $raw) {
                $this->pushTopic($topics, $this->cleanHtmlFragment($raw));
            }
        }

        if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $html, $liMatches)) {
            foreach ($liMatches[1] as $raw) {
                $text = $this->cleanHtmlFragment($raw);
                $text = preg_replace('/\s*\[.*?\]\s*/', '', $text) ?? $text;
                $this->pushTopic($topics, $text);
            }
        }

        if ($sourceKey === HealthTopicSourceCatalog::SOURCE_MEDLINEPLUS) {
            if (preg_match_all('/>([A-Z][A-Za-z0-9,\-\(\)\/\s]{2,80})</', strip_tags($html), $caps)) {
                foreach ($caps[1] as $candidate) {
                    $this->pushTopic($topics, trim($candidate));
                }
            }
        }

        return $this->uniqueTopics($topics);
    }

    /**
     * @param  list<string>  $topics
     */
    private function pushTopic(array &$topics, string $name): void
    {
        $name = HealthTopicSourceCatalog::normalizeTopicName($name);
        if ($name === '' || mb_strlen($name) < 3 || mb_strlen($name) > 255) {
            return;
        }
        if (preg_match('/^(home|search|menu|skip|about|click|back|read more|view all)$/i', $name)) {
            return;
        }
        $topics[] = $name;
    }

    private function cleanHtmlFragment(string $html): string
    {
        $text = strip_tags(html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * @param  list<string>  $topics
     * @return list<string>
     */
    private function uniqueTopics(array $topics): array
    {
        $seen = [];
        $unique = [];
        foreach ($topics as $topic) {
            $key = HealthTopicSourceCatalog::normalizeTagKey($topic);
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = HealthTopicSourceCatalog::normalizeTopicName($topic);
        }

        sort($unique, SORT_NATURAL | SORT_FLAG_CASE);

        return $unique;
    }
}
