<?php

namespace App\Support;

use App\Models\Forum;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * System context for Khub AI on the forums listing page.
 * Provides a catalog of visible threads and full thread detail when the user asks about specific discussions.
 */
final class ForumsListingAssistantContext
{
    private const MAX_CONTEXT_CHARS = 120000;

    private const MAX_FULL_THREADS = 2;

    /**
     * @param  list<int>  $forumIds
     */
    public static function buildForMessage(string $userMessage, array $forumIds): string
    {
        $forumIds = array_values(array_unique(array_filter(array_map('intval', $forumIds))));
        if ($forumIds === []) {
            return self::instructions()."\n\nNo forum threads are available on this page.";
        }

        $forums = Forum::query()
            ->whereIn('id', $forumIds)
            ->with([
                'user',
                'tags',
                'comments' => fn ($q) => $q->whereNull('parent_id')->orderBy('created_at')->limit(8),
                'comments.user',
                'comments.replies' => fn ($q) => $q->orderBy('created_at')->limit(5),
                'comments.replies.user',
            ])
            ->withCount([
                'comments as total_comments' => fn ($q) => $q->whereNull('parent_id'),
                'likes as total_likes',
            ])
            ->get()
            ->sortBy(fn (Forum $f) => array_search($f->id, $forumIds, true))
            ->values();

        $parts = [
            self::instructions(),
            '',
            '=== FORUMS ON THIS PAGE ('.$forums->count().' threads) ===',
            json_encode(self::catalogEntries($forums), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ];

        $matched = self::matchForumsForDetail($userMessage, $forums);
        foreach ($matched as $forum) {
            $parts[] = '';
            $parts[] = '=== FULL THREAD DETAIL (user asked about this discussion) ===';
            $parts[] = ForumAssistantContext::build($forum);
        }

        $text = implode("\n", $parts);
        if (strlen($text) > self::MAX_CONTEXT_CHARS) {
            $text = substr($text, 0, self::MAX_CONTEXT_CHARS)."\n\n[Context truncated for length.]";
        }

        return $text;
    }

    private static function instructions(): string
    {
        return implode("\n", [
            'You are Khub AI for the Africa Health Knowledge Hub discussion forums listing.',
            'You see a catalog of forum threads currently shown on this page, plus full thread detail when the user asks about specific discussions.',
            'Answer using ONLY the provided context. If a thread is not listed, say it is not on the current page and suggest opening that thread or refining the question.',
            'For overview questions, synthesize across multiple threads. For thread-specific questions, use the full thread detail section when available.',
            'Do not invent posts, comments, or attributions. Summarize comments without naming individuals unless essential.',
            'Format replies in Markdown only (no raw HTML): ### or #### headings, **bold**, and "-" bullet lists.',
            'When helpful, mention the thread title so the user knows which discussion you refer to.',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function catalogEntries(Collection $forums): array
    {
        $entries = [];
        foreach ($forums as $forum) {
            $description = strip_tags((string) ($forum->forum_description ?? ''));
            $description = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $sampleComments = [];
            foreach ($forum->comments->take(3) as $comment) {
                $text = strip_tags((string) ($comment->comment ?? ''));
                $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if (trim($text) === '') {
                    continue;
                }
                $sampleComments[] = Str::limit(trim($text), 220);
            }

            $entries[] = [
                'id' => $forum->id,
                'title' => strip_tags((string) ($forum->forum_title ?? '')),
                'author' => $forum->user->name ?? 'Unknown',
                'tags' => $forum->tags ? $forum->tags->pluck('tag')->filter()->values()->all() : [],
                'comments_count' => (int) ($forum->total_comments ?? 0),
                'likes_count' => (int) ($forum->total_likes ?? 0),
                'posted' => $forum->created_at ? $forum->created_at->toDateString() : null,
                'excerpt' => Str::limit(trim($description), 500),
                'sample_comments' => $sampleComments,
            ];
        }

        return $entries;
    }

    /**
     * @return Collection<int, Forum>
     */
    private static function matchForumsForDetail(string $userMessage, Collection $forums): Collection
    {
        $message = Str::lower(trim($userMessage));
        if ($message === '') {
            return collect();
        }

        $scored = [];
        foreach ($forums as $forum) {
            $score = self::scoreForumMatch($message, $forum);
            if ($score > 0) {
                $scored[] = ['forum' => $forum, 'score' => $score];
            }
        }

        if ($scored === []) {
            return collect();
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $topScore = $scored[0]['score'];
        if ($topScore < 3) {
            return collect();
        }

        $picked = [];
        foreach ($scored as $row) {
            if (count($picked) >= self::MAX_FULL_THREADS) {
                break;
            }
            if ($row['score'] >= max(3, (int) floor($topScore * 0.55))) {
                $picked[] = $row['forum'];
            }
        }

        return collect($picked);
    }

    private static function scoreForumMatch(string $message, Forum $forum): int
    {
        $title = Str::lower(strip_tags((string) ($forum->forum_title ?? '')));
        $score = 0;

        if ($title !== '' && str_contains($message, $title)) {
            $score += 20;
        }

        $titleWords = array_filter(preg_split('/\s+/', preg_replace('/[^a-z0-9\s]/', ' ', $title)) ?: []);
        foreach ($titleWords as $word) {
            if (strlen($word) < 4) {
                continue;
            }
            if (str_contains($message, $word)) {
                $score += 2;
            }
        }

        if ($forum->tags) {
            foreach ($forum->tags as $tag) {
                $tagText = Str::lower(trim((string) ($tag->tag ?? '')));
                if ($tagText !== '' && str_contains($message, $tagText)) {
                    $score += 4;
                }
            }
        }

        $detailPhrases = ['tell me more', 'more about', 'go deeper', 'in detail', 'that thread', 'that discussion', 'this thread', 'comments on', 'what did people say'];
        foreach ($detailPhrases as $phrase) {
            if (str_contains($message, $phrase)) {
                $score += 1;
            }
        }

        return $score;
    }
}
