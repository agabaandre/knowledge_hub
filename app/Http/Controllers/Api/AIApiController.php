<?php

namespace App\Http\Controllers\Api;

use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AIApiController extends Controller
{
    private $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * @OA\Post(
     * path="/api/ai/summarise",
     * operationId="summariseContentLegacy",
     * tags={"AI Operations"},
     * summary="Summarise content (one-shot)",
     * security={{"bearer_token":{}}},
     * description="Returns a one-shot summary for a forum thread or publication. For interactive Q&A with follow-ups, use **`POST /api/ai/chat`**, or **`POST /api/ai/assistant/session`** + **`POST /api/ai/assistant/message`** for a two-step flow.",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"resource_id", "type", "language"},
     *       @OA\Property(property="resource_id", type="integer", example=1),
     *       @OA\Property(property="type", type="integer", example=1, description="1 for forum, 2 for publication"),
     *       @OA\Property(property="language", type="string", example="en"),
     *       @OA\Property(property="prompt", type="string", example="Keep it short and concise")
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Successful",
     *    @OA\JsonContent()
     * )
     * )
     */
    public function summarise(Request $request)
    {
        return $this->aiService->summarise($request->resource_id, $request->type, $request->language, $request->prompt);
    }

    /**
     * @OA\Post(
     *     path="/api/ai/summarise-file",
     *     operationId="summariseUploadedFile",
     *     tags={"AI Operations"},
     *     summary="Summarise an uploaded file (publication wizard)",
     *     security={{"bearer_token":{}}},
     *     description="Same behaviour as web `POST /summarise-file` (auth): multipart upload of **pdf, doc, docx, or txt** (max 10 MB). Returns HTML `content` plus `metadata` `{ authors, affiliation }` extracted by AI. Use while composing a publication before save.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="language", type="string", example="en"),
     *                 @OA\Property(property="prompt", type="string", description="Optional extra instructions for the summariser")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="content + metadata JSON"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="AI or storage error")
     * )
     */
    public function summariseFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'language' => 'nullable|string|max:10',
            'prompt' => 'nullable|string|max:2000',
        ]);

        try {
            $file = $request->file('file');
            $language = $request->input('language', 'en');
            $tempPath = $file->storeAs('temp', uniqid().'_'.$file->getClientOriginalName(), 'public');
            $fullPath = storage_path('app/public/'.$tempPath);

            $result = $this->aiService->summariseFile($fullPath, $language, $request->input('prompt'));

            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return response()->json($result, 200);
        } catch (\Throwable $e) {
            Log::error('API summarise-file: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'content' => '<div class="alert alert-danger"><p>Error extracting summary: '.e($e->getMessage()).'</p></div>',
                'metadata' => ['authors' => '', 'affiliation' => ''],
            ], 500);
        }
    }
}
