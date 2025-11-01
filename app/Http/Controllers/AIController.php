<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    private $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function summarise(Request $request)
    {
        return $this->aiService->summarise($request->resource_id, $request->type, $request->language);
    }

    public function compare(Request $request)
    {
        return $this->aiService->compare($request->resource_id, $request->other_resource_id);
    }

    /**
     * Extract summary from uploaded file (used during publication creation)
     */
    public function summariseFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
            'language' => 'nullable|string|max:10'
        ]);

        try {
            $file = $request->file('file');
            $language = $request->input('language', 'en');
            
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
