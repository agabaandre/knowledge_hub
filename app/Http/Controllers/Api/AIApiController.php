<?php

namespace App\Http\Controllers\Api;

use App\Services\AIService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
     * summary="Summarise content (legacy one-shot)",
     * security={{"bearer_token":{}}},
     * description="Returns a one-shot summary for a forum thread or publication. For interactive Q&A, summaries with follow-ups, and comparisons in natural language, use `POST /api/ai/assistant/session` and `POST /api/ai/assistant/message` (Khub AI Assistant).",
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
}
