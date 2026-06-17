<?php

namespace App\Support;

use App\Models\Publication;
use App\Models\PublicationAttachment;

/**
 * Builds a text context for Khub AI on publications (GPT), including extracted PDF text
 * when no single PDF is available for ChatPDF. Multi-PDF resources should use ChatPDF
 * one document at a time instead of bundling all PDF text here.
 */
final class PublicationAssistantContext
{
    private const MAX_CONTEXT_CHARS = 120000;

    /** Rough cap per PDF so multiple attachments can share the budget. */
    private const MIN_CHARS_PER_PDF = 8000;

    public static function build(Publication $publication): string
    {
        $publication->loadMissing(['comments', 'attachments']);

        $title = strip_tags((string) ($publication->title ?? ''));
        $desc = strip_tags((string) ($publication->description ?? ''));
        $desc = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $link = (string) ($publication->publication ?? '');
        $linkExtract = '';
        if ($link !== '' && filter_var($link, FILTER_VALIDATE_URL)) {
            // External resource URL (may be PDF or other); keep legacy behaviour
            $linkExtract = truncate(pdfToText($link), 100000);
        }

        $nonPdfAttachmentLines = [];
        foreach ($publication->attachments ?? [] as $a) {
            if ($a->is_pdf) {
                continue;
            }
            $name = $a->original_filename ?? $a->description ?? 'attachment';
            $nonPdfAttachmentLines[] = '- '.$name.' | URL/path: '.($a->file ?? '');
        }

        $commentsJson = json_encode($publication->comments->toArray());

        $parts = [
            'You are Khub AI for the Africa Health Knowledge Hub.',
            'Answer ONLY using the resource context below. If the user asks something not covered, say so clearly.',
            'When multiple PDFs are provided below, you may summarise across all of them and attribute facts to the correct file when helpful.',
            'Use factual, health-appropriate language. Format every reply in Markdown only (no raw HTML tags): use ### or #### for section headings, **bold** for emphasis, and bullet lists with leading "- ". The chat UI will render Markdown as formatted text.',
            '',
            '=== RESOURCE TITLE ===',
            $title,
            '',
            '=== DESCRIPTION ===',
            $desc,
        ];

        $remaining = self::MAX_CONTEXT_CHARS - strlen(implode("\n", $parts)) - 5000;
        $pdfBlocks = self::buildPdfTextBlocks($publication, $remaining);
        if ($pdfBlocks !== '') {
            $parts[] = '';
            $parts[] = '=== PDF DOCUMENT TEXT (extracted; may be truncated per file) ===';
            $parts[] = $pdfBlocks;
        }

        if ($linkExtract !== '') {
            $parts[] = '';
            $parts[] = '=== TEXT EXTRACTED FROM LINKED RESOURCE (if any) ===';
            $parts[] = $linkExtract;
        }

        if ($nonPdfAttachmentLines !== []) {
            $parts[] = '';
            $parts[] = '=== NON-PDF ATTACHMENTS (filenames only) ===';
            $parts[] = implode("\n", $nonPdfAttachmentLines);
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

    /**
     * Concatenate extracted text for main PDF (if any) and each PDF attachment.
     */
    private static function buildPdfTextBlocks(Publication $publication, int $budget): string
    {
        $sources = $publication->pdf_sources;
        if ($sources === null || count($sources) === 0) {
            return '';
        }

        $n = count($sources);
        $perCap = max(self::MIN_CHARS_PER_PDF, (int) floor($budget / max(1, $n)));

        $out = [];
        foreach ($sources as $src) {
            $label = (string) ($src['label'] ?? 'Document');
            $idLabel = $src['type'] === 'main' ? 'main' : (string) ($src['attachment_id'] ?? '?');

            $raw = '';
            if (($src['type'] ?? '') === 'main') {
                $raw = self::extractMainPublicationPdfText($publication);
            } elseif (($src['type'] ?? '') === 'attachment' && ! empty($src['attachment_id'])) {
                $att = $publication->attachments->firstWhere('id', (int) $src['attachment_id']);
                if ($att instanceof PublicationAttachment && $att->is_pdf) {
                    $raw = self::extractAttachmentPdfText($att);
                }
            }

            $raw = trim((string) $raw);
            if ($raw === '') {
                $out[] = '['.$label.' (id '.$idLabel.'): no extractable text]';

                continue;
            }

            $chunk = mb_substr($raw, 0, $perCap);
            if (mb_strlen($raw) > $perCap) {
                $chunk .= "\n[…truncated]";
            }

            $out[] = '--- '.$label.' [source '.$idLabel.'] ---';
            $out[] = $chunk;
        }

        return implode("\n", $out);
    }

    private static function extractMainPublicationPdfText(Publication $publication): string
    {
        try {
            $path = $publication->publication_pdf_path;
            if (is_string($path) && $path !== '' && is_readable($path)) {
                return self::safePdfToText($path);
            }
            $url = $publication->publication_pdf_url;
            if (is_string($url) && $url !== '') {
                return self::safePdfToText($url);
            }
        } catch (\Throwable $e) {
            return '';
        }

        return '';
    }

    private static function extractAttachmentPdfText(PublicationAttachment $att): string
    {
        try {
            $path = $att->file_path;
            if (is_string($path) && $path !== '' && is_readable($path)) {
                return self::safePdfToText($path);
            }
            $url = $att->file_url ?? '';
            if (is_string($url) && $url !== '') {
                return self::safePdfToText($url);
            }
        } catch (\Throwable $e) {
            return '';
        }

        return '';
    }

    private static function safePdfToText(string $pathOrUrl): string
    {
        if (! function_exists('pdfToText')) {
            return '';
        }

        $out = @pdfToText($pathOrUrl);

        return is_string($out) ? $out : '';
    }
}
