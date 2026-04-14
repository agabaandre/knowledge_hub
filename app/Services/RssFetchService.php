<?php

namespace App\Services;

use App\Models\PublicationStaging;
use App\Models\RssFeed;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RssFetchService
{
    public function __construct(
        protected RssCategorizationService $categorization
    ) {}

    /**
     * Fetch one feed and persist new items to publications_staging.
     * Skips items already seen (by rss_guid or rss_link per feed).
     */
    public function fetchFeed(RssFeed $feed): array
    {
        $seen = $this->getSeenGuidsAndLinks($feed->id);
        $items = $this->fetchAndParse($feed->url);
        $created = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $guid = $item['guid'] ?? $item['link'] ?? null;
            $link = $item['link'] ?? $item['guid'] ?? null;
            if (!$guid && !$link) {
                continue;
            }
            $key = $guid ?: $link;
            if (isset($seen[$key])) {
                $skipped++;
                continue;
            }

            $metadata = $this->categorization->categorize(
                $item['title'] ?? 'Untitled',
                $item['description'] ?? '',
                $link
            );

            $staging = $this->mapToStaging($feed->id, $item, $metadata);
            PublicationStaging::create($staging);
            $created++;
            $seen[$key] = true;
        }

        $feed->update([
            'last_fetched_at' => now(),
            'last_fetch_status' => 'success',
            'last_fetch_message' => "Created: {$created}, Skipped: {$skipped}",
        ]);

        return ['created' => $created, 'skipped' => $skipped];
    }

    private function getSeenGuidsAndLinks(int $rssFeedId): array
    {
        $out = [];
        foreach (PublicationStaging::where('rss_feed_id', $rssFeedId)->get(['rss_guid', 'rss_link']) as $row) {
            if ($row->rss_guid) {
                $out[$row->rss_guid] = true;
            }
            if ($row->rss_link) {
                $out[$row->rss_link] = true;
            }
        }
        $links = \App\Models\Publication::where('is_rss', 1)->where('rss_id', $rssFeedId)->pluck('publication');
        foreach ($links as $link) {
            if ($link) {
                $out[$link] = true;
            }
        }
        return $out;
    }

    private function fetchAndParse(string $url): array
    {
        $items = [];
        try {
            $response = Http::timeout(25)->get($url);
            if (!$response->successful()) {
                return $items;
            }
            $xml = @simplexml_load_string($response->body());
            if ($xml === false) {
                return $items;
            }
            $xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
            $xml->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
            $xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
            $entries = $xml->channel->item ?? $xml->item ?? [];
            foreach ($entries as $entry) {
                $items[] = [
                    'title' => (string) ($entry->title ?? ''),
                    'link' => (string) ($entry->link ?? $entry->guid ?? ''),
                    'guid' => (string) ($entry->guid ?? $entry->link ?? ''),
                    'description' => (string) ($entry->description ?? $entry->children('content', true)->encoded ?? ''),
                    'pubDate' => (string) ($entry->pubDate ?? ''),
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('RSS fetch error: ' . $e->getMessage(), ['url' => $url]);
        }
        return $items;
    }

    private function mapToStaging(int $rssFeedId, array $item, array $metadata): array
    {
        $title = $item['title'] ?? $metadata['title'] ?? 'Untitled';
        $description = $metadata['description'] ?? $metadata['abstract'] ?? $item['description'] ?? '';
        $link = $item['link'] ?? '';
        $guid = $item['guid'] ?? $link;

        return [
            'rss_feed_id' => $rssFeedId,
            'rss_guid' => $guid ? substr($guid, 0, 500) : null,
            'rss_link' => $link ? substr($link, 0, 2048) : null,
            'processed_status' => PublicationStaging::STATUS_PENDING,
            'title' => $title,
            'description' => $description,
            'publication' => $link,
            'associated_authors' => $metadata['associated_authors'] ?? null,
            'author_affiliation' => $metadata['author_affiliation'] ?? null,
            'journal_name' => $metadata['journal_name'] ?? null,
            'journal_volume' => $metadata['journal_volume'] ?? null,
            'journal_issue' => $metadata['journal_issue'] ?? null,
            'journal_pages' => $metadata['journal_pages'] ?? null,
            'doi' => $metadata['doi'] ?? null,
            'issn' => $metadata['issn'] ?? null,
            'isbn' => $metadata['isbn'] ?? null,
            'publisher' => $metadata['publisher'] ?? null,
            'funder' => $metadata['funder'] ?? null,
            'copyright_info' => $metadata['copyright_info'] ?? null,
            'openai_metadata' => $metadata,
            'is_active' => 'Active',
            'is_approved' => 0,
            'is_rejected' => 0,
        ];
    }
}
