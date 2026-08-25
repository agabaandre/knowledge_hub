<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiUserDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersApiController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/users/me",
     *     operationId="GetCurrentUserDetails",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Get my user details",
     *     description="Returns the authenticated user's full account (same payload as `GET /api/profile`). Send `Authorization: Bearer {token}` from `POST /api/login`.",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $user = ApiUserDetails::loadForFull((int) $request->user()->id);
        if (! $user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => ApiUserDetails::fullDetails($user),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     operationId="GetUserDetails",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Get user details",
     *     description="Returns public profile fields for a user (name, photo, job, organisation, country, author). Email and phone are omitted. If a Bearer token is sent and the caller is that user (or has `alter_access_levels`), the full account payload is returned instead.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $auth = $request->user();
        $canSeeFull = $auth && (
            (int) $auth->id === $id || $auth->can('alter_access_levels')
        );

        if ($canSeeFull) {
            $user = ApiUserDetails::loadForFull($id);
            if (! $user) {
                return response()->json(['status' => 404, 'message' => 'User not found'], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => ApiUserDetails::fullDetails($user),
            ]);
        }

        $user = ApiUserDetails::loadForPublic($id);
        if (! $user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => ApiUserDetails::publicDetails($user),
        ]);
    }
}
