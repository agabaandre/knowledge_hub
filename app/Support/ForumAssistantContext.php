<?php

namespace App\Support;

use App\Models\Forum;

/**
 * System context for Khub AI on forum threads (aligned with AIService forum summarisation).
 */
final class ForumAssistantContext
{
    private const MAX_CONTEXT_CHARS = 120000;

    public static function build(Forum $forum): string
    {
        $forum->loadMissing([
            'user',
            'tags',
            'comments' => fn ($q) => $q->whereNull('parent_id')->orderBy('created_at'),
            'comments.user',
            'comments.likes',
            'comments.replies' => fn ($q) => $q->orderBy('created_at'),
            'comments.replies.user',
            'comments.replies.likes',
        ]);

        $structured = self::structuredComments($forum);
        $forumDescriptionText = strip_tags($forum->forum_description ?? '');
        $forumDescriptionText = html_entity_decode($forumDescriptionText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $tags = $forum->tags ? $forum->tags->pluck('tag')->filter()->implode(', ') : '';

        $parts = [
            'You are Khub AI for the Africa Health Knowledge Hub discussion forums.',
            'Answer using ONLY the forum thread context below. If something is not in the thread, say so. Do not invent discussion or attributions.',
            'Use factual, health-appropriate language. Format every reply in Markdown only (no raw HTML tags): use ### or #### for section headings, **bold** for emphasis, and bullet lists with leading "- ". The chat UI will render Markdown as formatted text.',
            '',
            '=== FORUM TITLE ===',
            strip_tags((string) ($forum->forum_title ?? '')),
            '',
            '=== TAGS ===',
            $tags,
            '',
            '=== ORIGINAL POST ===',
            $forumDescriptionText,
            '',
            '=== COMMENTS (structured JSON; do not name individuals in summaries unless essential) ===',
            json_encode($structured, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ];

        $text = implode("\n", $parts);
        if (strlen($text) > self::MAX_CONTEXT_CHARS) {
            $text = substr($text, 0, self::MAX_CONTEXT_CHARS)."\n\n[Context truncated for length.]";
        }

        return $text;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function structuredComments(Forum $forum): array
    {
        $out = [];
        foreach ($forum->comments as $comment) {
            if ($comment->parent_id) {
                continue;
            }
            $commentText = strip_tags($comment->comment ?? '');
            $commentText = html_entity_decode($commentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $commentText = trim($commentText);

            $replies = [];
            foreach ($comment->replies ?? [] as $reply) {
                $replyText = strip_tags($reply->comment ?? '');
                $replyText = html_entity_decode($replyText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $replies[] = [
                    'user' => $reply->user->name ?? 'Unknown',
                    'text' => trim($replyText),
                    'likes' => $reply->likes ? $reply->likes->count() : 0,
                ];
            }

            $out[] = [
                'user' => $comment->user->name ?? 'Unknown',
                'text' => $commentText,
                'likes' => $comment->likes ? $comment->likes->count() : 0,
                'replies_count' => $comment->replies ? $comment->replies->count() : 0,
                'replies' => $replies,
            ];
        }

        return $out;
    }
}
