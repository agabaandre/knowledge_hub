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
     * operationId="Summarise Content",
     * tags={"AI Operations"},
     * summary="Summarise Content",
     * description="Summarises the content of a publication or forum",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"resource_id", "type", "language"},
     *       @OA\Property(property="resource_id", type="integer", example=1),
     *       @OA\Property(property="type", type="integer", example=1, description="1 for forum, 2 for publication"),
     *       @OA\Property(property="language", type="string", example="en")
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
        return $this->aiService->summarise($request->resource_id, $request->type, $request->language);
    }

    /**
     * @OA\Post(
     * path="/api/ai/compare",
     * operationId="Compare Content",
     * tags={"AI Operations"},
     * summary="Compare Content",
     * description="Compares the content of two publications",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"resource_id", "other_resource_id"},
     *       @OA\Property(property="resource_id", type="integer", example=1),
     *       @OA\Property(property="other_resource_id", type="integer", example=2)
     * @OA\Property(property="prompt", type="string", example="Keep it short and concise")
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Successful",
     *    @OA\JsonContent()
     * )
     * )
     */
    public function compare(Request $request)
    {
        return $this->aiService->compare($request->resource_id, $request->other_resource_id);
    }
}
