<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AIController extends Controller
{
    private $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function summarise(Request $request)
    {
        $language = resolve_ai_locale($request->input('language'));

        return $this->aiService->summarise(
            $request->resource_id,
            $request->type,
            publication_language_label($language),
            $request->input('prompt')
        );
    }

    /**
     * Stream summary response (for AI summarizer modal).
     */
    public function summariseStream(Request $request): StreamedResponse
    {
        $request->validate([
            'resource_id' => 'required',
            'type' => 'required',
            'language' => 'nullable|string|max:20',
            'prompt' => 'nullable|string|max:1000',
        ]);

        $language = publication_language_label(resolve_ai_locale($request->input('language')));

        return new StreamedResponse(function () use ($request, $language) {
            $this->aiService->summariseStream(
                $request->resource_id,
                $request->type,
                $language,
                $request->input('prompt'),
                function ($chunk) {
                    echo $chunk;
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                }
            );
        }, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Extract summary from uploaded file (used during publication creation)
     */
    public function summariseFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
            'language' => 'nullable|string|max:32',
        ]);

        try {
            $file = $request->file('file');
            $rawLanguage = $request->input('language');
            $language = (is_string($rawLanguage) && strtolower(trim($rawLanguage)) === 'document')
                ? 'document'
                : resolve_ai_locale(is_string($rawLanguage) ? $rawLanguage : null);

            // Store file temporarily
            $temp_path = $file->storeAs('temp', uniqid() . '_' . $file->getClientOriginalName(), 'public');
            $full_path = storage_path('app/public/' . $temp_path);

            // Extract summary
            $result = $this->aiService->summariseFile($full_path, $language);

            // Clean up temp file
            if (file_exists($full_path)) {
                unlink($full_path);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error("Error in summariseFile: " . $e->getMessage());
            return response()->json([
                'content' => '<div class="alert alert-danger">Error extracting summary: ' . $e->getMessage() . '</div>',
                'metadata' => ['authors' => '', 'affiliation' => '']
            ], 500);
        }
    }
}
