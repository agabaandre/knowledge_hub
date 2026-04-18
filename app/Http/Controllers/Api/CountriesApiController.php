<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AreasRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Countries",
 *     description="Member states and country-level engagement statistics (aligned with the public countries map and details pages)."
 * )
 */
class CountriesApiController extends Controller
{
    public function __construct(private AreasRepository $areasRepo)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/countries",
     *     operationId="listMemberStatesWithStats",
     *     tags={"Countries"},
     *     summary="Member states with statistics",
     *     description="Returns all member states (countries with a region) and counts of linked publications, forum threads started by users from that country, and enrolled users (`users.country_id`). Publication counts use the same filters as the public country details list (active, approved, non-version, not admin-only).",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->areasRepo->memberStatesWithStats();

        return response()->json([
            'status' => 200,
            'message' => 'Member states with engagement statistics.',
            'data' => $data,
        ], 200);
    }
}
