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
                $content = $msg['content'] ?? '';
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
            'all_messages.*.role' => 'string|in:user,assistant',
            'all_messages.*.content' => 'string',
        ]);
        if (! $request->filled('content') && ! $request->filled('all_messages')) {
            return response()->json(['error' => 'No content to export.'], 422);
        }

        $html = $this->buildHtml($request);
        $title = $request->input('title', 'Chat export');
        $filename = preg_replace('/[^\pL\pN\s\-]/u', '', $title);
        $filename = preg_replace('/\s+/', '-', trim($filename)) ?: 'chat-export';
        $filename = substr($filename, 0, 80) . '.pdf';

        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
            ]);
            $mpdf->SetTitle($title);
            $mpdf->WriteHTML($html);
            return response()->streamDownload(function () use ($mpdf) {
                echo $mpdf->Output('', 'S');
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'PDF export failed.'], 500);
        }
    }

    /**
     * Export as Word (single content or all_messages).
     */
    public function exportWord(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'all_messages' => 'nullable|array',
            'all_messages.*.role' => 'string|in:user,assistant',
            'all_messages.*.content' => 'string',
        ]);
        if (! $request->filled('content') && ! $request->filled('all_messages')) {
            return response()->json(['error' => 'No content to export.'], 422);
        }

        $title = $request->input('title', 'Chat export');
        $filename = preg_replace('/[^\pL\pN\s\-]/u', '', $title);
        $filename = preg_replace('/\s+/', '-', trim($filename)) ?: 'chat-export';
        $filename = substr($filename, 0, 80) . '.docx';

        try {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            $section->addTitle(htmlspecialchars($title), 1);

            if ($request->has('all_messages') && is_array($request->all_messages)) {
                foreach ($request->all_messages as $msg) {
                    $role = $msg['role'] ?? 'assistant';
                    $content = $msg['content'] ?? '';
                    $label = $role === 'user' ? 'You' : 'Assistant';
                    $section->addText(htmlspecialchars($label), ['bold' => true, 'size' => 10]);
                    $section->addTextBreak(1);
                    PhpWordHtml::addHtml($section, $this->markdownToHtml($content));
                    $section->addTextBreak(2);
                }
            } else {
                $content = $request->input('content', '');
                PhpWordHtml::addHtml($section, $this->markdownToHtml($content));
            }

            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
            $objWriter->save($tempFile);

            return response()->streamDownload(function () use ($tempFile) {
                echo file_get_contents($tempFile);
                @unlink($tempFile);
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Word export failed.'], 500);
        }
    }
}
