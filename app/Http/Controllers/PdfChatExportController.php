<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html as PhpWordHtml;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfChatExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Convert markdown-like text to HTML for export.
     */
    private function markdownToHtml(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '<p></p>';
        }
        $s = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $s = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $s);
        $s = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $s);
        $s = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $s);
        $s = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $s);
        $s = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $s);
        $s = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $s);
        $s = preg_replace('/^---\s*$/m', '<hr>', $s);
        $s = preg_replace('/^-\s+(.+)$/m', '<li>$1</li>', $s);
        $s = preg_replace('/(<li>.*?<\/li>\s*)+/s', '<ul>$0</ul>', $s);
        $s = preg_replace('/\n\n+/', '</p><p>', $s);
        $s = nl2br($s);
        if (strpos($s, '<p>') !== 0) {
            $s = '<p>' . $s . '</p>';
        }
        return $s;
    }

    /**
     * Build full HTML for PDF from title and content or all_messages.
     */
    private function buildHtml(Request $request): string
    {
        $title = $request->input('title', 'Chat export');
        $title = is_string($title) ? trim($title) : 'Chat export';

        if ($request->has('all_messages') && is_array($request->all_messages)) {
            $html = '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;">';
            $html .= '<h1 style="font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem;">' . htmlspecialchars($title) . '</h1>';
            $html .= '<p style="color: #6c757d; font-size: 0.875rem; margin-bottom: 1.5rem;">Full conversation export</p>';
            foreach ($request->all_messages as $msg) {
                $role = $msg['role'] ?? 'assistant';
                $content = isset($msg['content']) ? (string) $msg['content'] : '';
                $label = $role === 'user' ? 'You' : 'Assistant';
                $bg = $role === 'user' ? '#e7f1ff' : '#f8f9fa';
                $html .= '<div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: ' . $bg . '; border-radius: 8px; border-left: 3px solid #0d6efd;">';
                $html .= '<div style="font-size: 0.75rem; font-weight: 600; margin-bottom: 0.35rem; color: #495057;">' . htmlspecialchars($label) . '</div>';
                $html .= '<div style="line-height: 1.5;">' . $this->markdownToHtml($content) . '</div></div>';
            }
            $html .= '</div>';
            return $html;
        }

        $content = $request->input('content', '');
        $content = is_string($content) ? $content : '';
        $html = '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;">';
        $html .= '<h1 style="font-size: 1.5rem; margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem;">' . htmlspecialchars($title) . '</h1>';
        $html .= '<div style="line-height: 1.6;">' . $this->markdownToHtml($content) . '</div></div>';
        return $html;
    }

    /**
     * Export as PDF (single content or all_messages).
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'all_messages' => 'nullable|array',
            'all_messages.*.role' => 'nullable|string|in:user,assistant',
            'all_messages.*.content' => 'nullable|string',
        ]);
        if (! $request->filled('content') && ! $request->filled('all_messages')) {
            return response()->json(['error' => 'No content to export.'], 422);
        }

        $html = $this->buildHtml($request);
        $title = is_string($request->input('title')) ? trim($request->input('title')) : 'Chat export';
        $title = $title !== '' ? $title : 'Chat export';
        $filename = preg_replace('/[^\pL\pN\s\-]/u', '', $title);
        $filename = preg_replace('/\s+/', '-', trim($filename)) ?: 'chat-export';
        $filename = substr($filename, 0, 80) . '.pdf';

        try {
            $tempDir = storage_path('app/tmp');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'tempDir' => $tempDir,
            ]);
            $mpdf->SetTitle($title);
            $mpdf->WriteHTML($html);
            $pdfBlob = $mpdf->Output('', 'S');
            return response($pdfBlob, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'PDF export failed.'], 500);
        }
    }

    /**
     * Export as Word (single content or all_messages).
     */
    /**
     * Safe HTML for PhpWord: strip problematic tags, escape ampersands, ensure valid fragment.
     */
    private function safeHtmlForWord(string $html): string
    {
        $html = strip_tags($html, '<p><br><strong><em><b><i><ul><ol><li><h1><h2><h3><h4><hr>');
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/&(?!amp;|lt;|gt;|quot;|#\d+;)/', '&amp;', $html);
        return trim($html) !== '' ? $html : '<p>No content</p>';
    }

    /**
     * Add HTML to PhpWord section with fallback to plain text if addHtml fails.
     */
    private function addHtmlToSection($section, string $html): void
    {
        try {
            PhpWordHtml::addHtml($section, $html);
        } catch (\Throwable $e) {
            $plain = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html)));
            $section->addText($plain !== '' ? $plain : ' ');
        }
    }

    public function exportWord(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'all_messages' => 'nullable|array',
            'all_messages.*.role' => 'nullable|string|in:user,assistant',
            'all_messages.*.content' => 'nullable|string',
        ]);
        if (! $request->filled('content') && ! $request->filled('all_messages')) {
            return response()->json(['error' => 'No content to export.'], 422);
        }

        $title = is_string($request->input('title')) ? trim($request->input('title')) : 'Chat export';
        $title = $title !== '' ? $title : 'Chat export';
        $filename = preg_replace('/[^\pL\pN\s\-]/u', '', $title);
        $filename = preg_replace('/\s+/', '-', trim($filename)) ?: 'chat-export';
        $filename = substr($filename, 0, 80) . '.docx';

        try {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            $section->addText($title, ['bold' => true, 'size' => 16]);
            $section->addTextBreak(1);

            if ($request->has('all_messages') && is_array($request->all_messages)) {
                foreach ($request->all_messages as $msg) {
                    $role = $msg['role'] ?? 'assistant';
                    $content = isset($msg['content']) ? (string) $msg['content'] : '';
                    $label = $role === 'user' ? 'You' : 'Assistant';
                    $section->addText($label, ['bold' => true, 'size' => 10]);
                    $section->addTextBreak(1);
                    $html = $this->safeHtmlForWord($this->markdownToHtml($content));
                    $this->addHtmlToSection($section, $html);
                    $section->addTextBreak(2);
                }
            } else {
                $content = (string) $request->input('content', '');
                $html = $this->safeHtmlForWord($this->markdownToHtml($content));
                $this->addHtmlToSection($section, $html);
            }

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $tempDir = storage_path('app/tmp');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            $tempFile = $tempDir . '/phpword_' . uniqid('', true) . '.docx';
            $objWriter->save($tempFile);

            $content = file_get_contents($tempFile);
            @unlink($tempFile);

            return response($content, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Word export failed.'], 500);
        }
    }
}
