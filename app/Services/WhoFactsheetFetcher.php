<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhoFactsheetFetcher
{
    private const HEALTH_TOPIC_BASE = 'https://www.who.int/health-topics/';

    private const FACT_SHEET_BASE = 'https://www.who.int/news-room/fact-sheets/detail/';

    /**
     * @return array{
     *     ok: bool,
     *     topic: string,
     *     health_topic_url: ?string,
     *     fact_sheet_url: ?string,
     *     excerpt: string,
     *     sections: list<array{heading: string, body: string}>,
     *     references: list<array{label: string, url: string}>
     * }
     */
    public function fetchForTopic(string $topicName): array
    {
        $topicName = trim($topicName);
        $empty = [
            'ok' => false,
            'topic' => $topicName,
            'health_topic_url' => null,
            'fact_sheet_url' => null,
            'excerpt' => '',
            'sections' => [],
            'references' => [],
        ];

        if ($topicName === '') {
            return $empty;
        }

        $factSheet = $this->fetchFirstSuccessful($this->factSheetUrlCandidates($topicName));
        $healthTopic = $this->fetchFirstSuccessful($this->healthTopicUrlCandidates($topicName));

        $html = (string) ($factSheet['html'] ?? '');
        $factSheetUrl = $factSheet['url'] ?? null;
        $healthTopicUrl = $healthTopic['url'] ?? null;

        if ($html === '' && ! empty($healthTopic['html'])) {
            $html = (string) $healthTopic['html'];
        }

        if ($html === '') {
            return $empty;
        }

        $sections = $this->extractH2Sections($html);
        $meta = $this->extractMetaDescription($html);
        $excerptParts = [];
        if ($meta !== '') {
            $excerptParts[] = $meta;
        }
        foreach ($sections as $section) {
            $line = trim($section['heading'].': '.$section['body']);
            if ($line !== ':' && mb_strlen($line) > 20) {
                $excerptParts[] = $line;
            }
        }

        $excerpt = Str::limit(implode("\n\n", $excerptParts), 12000, '');

        $references = [];
        if ($factSheetUrl) {
            $references[] = [
                'label' => 'WHO fact sheet: '.$topicName,
                'url' => $factSheetUrl,
            ];
        }
        if ($healthTopicUrl && $healthTopicUrl !== $factSheetUrl) {
            $references[] = [
                'label' => 'WHO health topic: '.$topicName,
                'url' => $healthTopicUrl,
            ];
        }
        $references[] = [
            'label' => 'WHO Health Topics',
            'url' => 'https://www.who.int/health-topics',
        ];

        return [
            'ok' => $excerpt !== '',
            'topic' => $topicName,
            'health_topic_url' => $healthTopicUrl,
            'fact_sheet_url' => $factSheetUrl,
            'excerpt' => $excerpt,
            'sections' => $sections,
            'references' => $references,
        ];
    }

    /**
     * @return list<string>
     */
    private function factSheetUrlCandidates(string $topicName): array
    {
        return $this->urlCandidates($topicName, self::FACT_SHEET_BASE);
    }

    /**
     * @return list<string>
     */
    private function healthTopicUrlCandidates(string $topicName): array
    {
        return $this->urlCandidates($topicName, self::HEALTH_TOPIC_BASE);
    }

    /**
     * @return list<string>
     */
    private function urlCandidates(string $topicName, string $base): array
    {
        $candidates = [];
        $slug = Str::slug($topicName);
        if ($slug !== '') {
            $candidates[] = $base.$slug;
        }

        $normalized = preg_replace('/\s*\([^)]*\)\s*/u', ' ', $topicName) ?? $topicName;
        $normalizedSlug = Str::slug(trim($normalized));
        if ($normalizedSlug !== '' && $normalizedSlug !== $slug) {
            $candidates[] = $base.$normalizedSlug;
        }

        $firstWord = strtok($normalized, " \t\n\r/");
        if (is_string($firstWord) && $firstWord !== '') {
            $firstSlug = Str::slug($firstWord);
            if ($firstSlug !== '' && ! in_array($base.$firstSlug, $candidates, true)) {
                $candidates[] = $base.$firstSlug;
            }
        }

        if (stripos($topicName, 'hiv') !== false) {
            $candidates[] = $base.'hiv-aids';
        }
        if (stripos($topicName, 'covid') !== false) {
            $candidates[] = $base.'coronavirus-disease-(covid-19)';
        }

        return array_values(array_unique($candidates));
    }

    /**
     * @param  list<string>  $urls
     * @return array{url: ?string, html: ?string}
     */
    private function fetchFirstSuccessful(array $urls): array
    {
        foreach ($urls as $url) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => 'AfricaCDC-KnowledgeHub/1.0 (+who-factsheet-fetch)'])
                    ->get($url);
                if (! $response->successful()) {
                    continue;
                }
                $html = (string) $response->body();
                if ($this->looksLikeWhoTopicPage($html)) {
                    return ['url' => $url, 'html' => $html];
                }
            } catch (\Throwable $e) {
                Log::debug('WhoFactsheetFetcher: fetch failed', ['url' => $url, 'error' => $e->getMessage()]);
            }
        }

        return ['url' => null, 'html' => null];
    }

    private function looksLikeWhoTopicPage(string $html): bool
    {
        if (stripos($html, 'who.int') === false) {
            return false;
        }

        return stripos($html, 'healthtopic') !== false
            || stripos($html, 'fact-sheet') !== false
            || stripos($html, 'Key facts') !== false
            || preg_match('/<h2[^>]*>.*?Overview/is', $html) === 1;
    }

    private function extractMetaDescription(string $html): string
    {
        if (preg_match('/name=["\']description["\']\s+content=["\']([^"\']+)["\']/i', $html, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if (preg_match('/property=["\']og:description["\']\s+content=["\']([^"\']+)["\']/i', $html, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    /**
     * @return list<array{heading: string, body: string}>
     */
    private function extractH2Sections(string $html): array
    {
        $sections = [];
        if (! preg_match_all('/<h2[^>]*>(.*?)<\/h2>(.*?)(?=<h2|$)/is', $html, $matches, PREG_SET_ORDER)) {
            return $sections;
        }

        foreach ($matches as $match) {
            $heading = $this->cleanText($match[1] ?? '');
            $body = $this->cleanText($match[2] ?? '');
            if ($heading === '' || mb_strlen($body) < 40) {
                continue;
            }
            if (preg_match('/^(related|more|navigation|share|download)/i', $heading)) {
                continue;
            }
            $sections[] = [
                'heading' => $heading,
                'body' => Str::limit($body, 2500, ''),
            ];
            if (count($sections) >= 8) {
                break;
            }
        }

        return $sections;
    }

    private function cleanText(string $html): string
    {
        $text = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $text = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $text) ?? $text;
        $text = strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
