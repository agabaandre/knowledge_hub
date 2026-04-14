<?php

namespace App\Support;

use App\Models\Publication;

/**
 * Builds a text context for Khub AI Assistant on non-PDF resources (same signals as summarise-stream).
 */
final class PublicationAssistantContext
{
    private const MAX_CONTEXT_CHARS = 120000;

    public static function build(Publication $publication): string
    {
        $publication->loadMissing(['comments', 'attachments']);

        $title = strip_tags((string) ($publication->title ?? ''));
        $desc = strip_tags((string) ($publication->description ?? ''));
        $desc = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $link = (string) ($publication->publication ?? '');
        $linkExtract = '';
        if ($link !== '' && filter_var($link, FILTER_VALIDATE_URL)) {
            $linkExtract = truncate(pdfToText($link), 100000);
        }

        $attachmentLines = [];
        foreach ($publication->attachments ?? [] as $a) {
            $name = $a->original_filename ?? $a->description ?? 'attachment';
            $attachmentLines[] = '- '.$name.' | URL/path: '.($a->file ?? '');
        }

        $commentsJson = json_encode($publication->comments->toArray());

        $parts = [
            'You are Khub AI Assistant for the Africa Health Knowledge Hub.',
            'Answer ONLY using the resource context below. If the user asks something not covered, say so clearly.',
            'Use factual, health-appropriate language. Format every reply in Markdown only (no raw HTML tags): use ### or #### for section headings, **bold** for emphasis, and bullet lists with leading "- ". The chat UI will render Markdown as formatted text.',
            '',
            '=== RESOURCE TITLE ===',
            $title,
            '',
            '=== DESCRIPTION ===',
            $desc,
        ];

        if ($linkExtract !== '') {
            $parts[] = '';
            $parts[] = '=== TEXT EXTRACTED FROM LINKED RESOURCE (if any) ===';
            $parts[] = $linkExtract;
        }

        if ($attachmentLines !== []) {
            $parts[] = '';
            $parts[] = '=== ATTACHMENTS (filenames; full file content may not be available) ===';
            $parts[] = implode("\n", $attachmentLines);
        }

        $parts[] = '';
        $parts[] = '=== COMMENTS (JSON; do not attribute to named individuals in summaries) ===';
        $parts[] = $commentsJson;

        $text = implode("\n", $parts);
        if (strlen($text) > self::MAX_CONTEXT_CHARS) {
            $text = substr($text, 0, self::MAX_CONTEXT_CHARS)."\n\n[Context truncated for length.]";
        }

        return $text;
    }
}
